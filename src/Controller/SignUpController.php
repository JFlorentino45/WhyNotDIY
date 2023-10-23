<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\SignupType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            
            $user->setPasswordHash($hashedPassword); // Note: Consider renaming this method to reflect its purpose better (e.g., setHashedPassword).
            $user->setRole('user');
            // Save the user to the database
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home'); // Change to your desired route.
        }

        return $this->render('auth/signup.html.twig', [
            'controller_name' => 'SignUpController',
            'form' => $form->createView(),
        ]);
    }
}
