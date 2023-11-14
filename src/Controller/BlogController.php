<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/create-blog', name: 'create_blog')]
    public function createBlog(Request $request): Response
    {
    $blog = new Blog();
    $form = $this->createForm(BlogType::class, $blog);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->persist($blog);
        $this->entityManager->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('blog/create.html.twig', [
        'form' => $form->createView(),
    ]);
    }
    #[Route('/blog/{id}', name: 'view_blog')]
    public function viewBlog(Request $request, Blog $blog): Response
    {
        return $this->render('blog/index.html.twig', [
            'blog' => $blog,
        ]);
    }
}