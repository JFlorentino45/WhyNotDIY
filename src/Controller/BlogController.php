<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class BlogController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
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
    public function viewBlog(Blog $blog): Response
    {
        return $this->render('blog/index.html.twig', [
            'blog' => $blog,
        ]);
    }
    #[Route('/myBlogs', name: 'my_blogs')]
    public function myBlogs(): Response
    {
        $user = $this->security->getUser();
        $blogs = $this->entityManager->getRepository(Blog::class)->findBy(['createdBy' => ($user)]);

        return $this->render('blog/myBlogs.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'blogs' => $blogs,
        ]);
    }
    
}