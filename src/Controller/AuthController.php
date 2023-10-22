<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/signup', name: 'app_auth')]
    public function index(): Response
    {
        return $this->render('auth/signup.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
}
