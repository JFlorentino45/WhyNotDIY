<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BlogRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/blogs', name: 'app_admin_blogs', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('admin/blogs.html.twig', [
            'blogs' => $blogRepository->findAllOrderedByLatest(),
        ]);
    }
}
