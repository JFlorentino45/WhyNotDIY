<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\SignupType;
use App\Entity\User;
use App\Entity\AdminNotification;
use App\Service\BlacklistService;
use App\Service\ForbiddenWordService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

class SignUpController extends AbstractController
{
    private $forbiddenWordService;
    private $blacklistService;
    private $entityManager;
    private $passwordHasher;

    public function __construct(
        ForbiddenWordService $forbiddenWordService,
        EntityManagerInterface $entityManager,
        BlacklistService $blacklistService,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->forbiddenWordService = $forbiddenWordService;
        $this->entityManager = $entityManager;
        $this->blacklistService = $blacklistService;
    }

    #[Route('/signup', name: 'app_signup', methods: ['GET', 'POST'])]
    public function index(Request $request, Recaptcha3Validator $recaptcha3Validator): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if ($score <= 0.5) {
                return $this->redirectToRoute('app_blog_index');
            }
            $email = $form->get('emailAddress')->getData();
            if ($this->blacklistService->isBanned($email)) {
                $this->addFlash('error', '*This E-mail is banned.');
            } else {
            $username = $form->get('userName')->getData();
            if ($this->forbiddenWordService->isForbidden($username)) {
                $this->addFlash('error', '*Username contains forbidden words.');
            } else {
                $service = $this->forbiddenWordService->containsForbiddenWord($username);
                if ($service['found']) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("$username may have a forbidden word in their username. Please verify.");
                    $adminNotification->setBlog(null);
                    $adminNotification->setWords($service['word']);
                    $adminNotification->setComment(null);

                    $password = $form->get('plainPassword')->getData();
                    $validation = $form->get('confirmPassword')->getData();
                    if ($password !== $validation) {
                        $this->addFlash('error', '*Passwords do not match');
                    } else {
                        
                        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                        $user->setPasswordHash($hashedPassword);
                        $user->setRole('ROLE_user');
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        $adminNotification->setUser($user);
                        $this->entityManager->persist($adminNotification);
                        $this->entityManager->flush();

                        $this->addFlash('success', '*Account created, Please login.');
                        return $this->redirectToRoute('app_login');
                    }
                } else {
                    $password = $form->get('plainPassword')->getData();
                    $validation = $form->get('confirmPassword')->getData();
                    if ($password !== $validation) {
                        $this->addFlash('error', '*Passwords do not match');
                    } else {
                        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                        $user->setPasswordHash($hashedPassword);
                        $user->setRole('ROLE_user');
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        
                        $this->addFlash('success', '*Account created, Please login.');
                        return $this->redirectToRoute('app_login');
                    }
                }
            }
            }

        }
        return $this->render('security/signup.html.twig', [
            'controller_name' => 'SignUpController',
            'form' => $form->createView(),
        ]);
    }
}