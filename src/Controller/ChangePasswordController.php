<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordController extends AbstractController
{
    #[Route('/edit/password', name: 'app_change_password', methods: ['POST', 'GET'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Recaptcha3Validator $recaptcha3Validator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if ($score <= 0.5) {
                return $this->redirectToRoute('logout');
            }
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('plainPassword')->getData();
            $validation = $form->get('confirmPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', '*Incorrect current password');
            } elseif ($newPassword !== $validation) {
                $this->addFlash('error', '*Passwords do not match');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPasswordHash($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', '*Password updated successfully');


                return $this->redirectToRoute('app_blog_index');
            }
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
