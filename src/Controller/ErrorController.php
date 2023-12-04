<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/access-denied', name: 'access_denied', methods:['POST'])]
    public function error403(): Response
    {
        return $this->render('error/access_denied.html.twig', [], new Response('', 403));
    }
}
