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

class SignUpController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ForbiddenWordService $forbiddenWordService, BlacklistService $blacklist): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('emailAddress')->getData();
            if ($blacklist->isBanned($email)) {
                $this->addFlash('error', 'This E-mail is banned.');
            } else {
            $username = $form->get('userName')->getData();
            if ($forbiddenWordService->isForbidden($username)) {
                $this->addFlash('error', 'Username contains forbidden words.');
            } else {
                if ($forbiddenWordService->containsForbiddenWord($username)) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("$username may have a forbidden word in their username. Please verify.");
                    $adminNotification->setBlog(null);
                    $adminNotification->setComment(null);

                    $password = $form->get('plainPassword')->getData();
                    $validation = $form->get('confirmPassword')->getData();
                    if ($password !== $validation) {
                        $this->addFlash('error', 'Passwords do not match');
                    } else {
                        $hashedPassword = $passwordHasher->hashPassword($user, $password);
                        $user->setPasswordHash($hashedPassword);
                        $user->setRole('ROLE_user');
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $adminNotification->setUser($user);
                        $entityManager->persist($adminNotification);
                        $entityManager->flush();

                        $this->addFlash('success', 'Account created, Please login.');
                        return $this->redirectToRoute('app_login');
                    }
                } else {
                    $password = $form->get('plainPassword')->getData();
                    $validation = $form->get('confirmPassword')->getData();
                    if ($password !== $validation) {
                        $this->addFlash('error', 'Passwords do not match');
                    } else {
                        $hashedPassword = $passwordHasher->hashPassword($user, $password);
                        $user->setPasswordHash($hashedPassword);
                        $user->setRole('ROLE_user');
                        $entityManager->persist($user);
                        $entityManager->flush();
                        
                        $this->addFlash('success', 'Account created, Please login.');
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