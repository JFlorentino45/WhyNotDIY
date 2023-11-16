<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BlogRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/my-blogs', name: 'app_blog_mine')]
    public function myBlogs(Security $security, BlogRepository $blogRepository): Response
    {
        $user = $security->getUser();
    
        return $this->render('blog/myBlogs.html.twig', [
            'blogs' => $blogRepository->findBy(['createdBy' => ($user)]),
        ]);
    }
    
    #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user !== $blog->getCreatedBy() && !$this->isGranted('ROLE_admin')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(BlogType::class, $blog);
        $oldData = clone $blog;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($blog->isModified($oldData)) {
                $entityManager->flush();
                return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('warning', 'No changes detected.');
                return $this->redirectToRoute('app_blog_edit', ['id' => $blog->getId()]);
            }
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }

}
