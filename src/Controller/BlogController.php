<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Likes;
use App\Entity\Comments;
use App\Entity\AdminNotification;
use App\Form\CommentType;
use App\Form\BlogType;
use App\Repository\CommentsRepository;
use App\Repository\BlogRepository;
use App\Repository\AdminNotificationRepository;
use App\Service\ForbiddenWordService;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ForbiddenWordService $forbiddenWordService): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->get('title')->getData();
            $text = $form->get('text')->getData();
            if ($forbiddenWordService->isForbidden($title) || $forbiddenWordService->isForbidden($text)) {
                $this->addFlash('error', 'Blog contains forbidden words.');
            } else {
                if ($forbiddenWordService->containsForbiddenWord($title) || $forbiddenWordService->containsForbiddenWord($text)) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $message = "";
                    if ($forbiddenWordService->containsForbiddenWord($title) && $forbiddenWordService->containsForbiddenWord($text)) {
                        $message = "A blog's title and text may contain forbidden words. Please verity";
                    } elseif ($forbiddenWordService->containsForbiddenWord($text)) {
                        $message = "A blog's text may contain a forbidden word. Please verify.";
                    } else {
                        $message = "A blog title may contain a forbidden word. Please verify.";
                    }
                    $adminNotification->setText($message);
                    $adminNotification->setUser(null);
                    $adminNotification->setComment(null);
                    
                    $entityManager->persist($blog);
                    $entityManager->flush();
                    
                    $adminNotification->setBlog($blog);
                    $entityManager->persist($adminNotification);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Blog created.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                } else {
                    $entityManager->persist($blog);
                    $entityManager->flush();
                    $this->addFlash('success', 'Blog created.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                }
            }
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
    
    #[Route('/{id}', name: 'app_blog_show', methods: ['GET', 'POST'])]
    public function show(Blog $blog, Request $request, EntityManagerInterface $entityManager, CommentsRepository $commentsRepository, ForbiddenWordService $forbiddenWordService): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash('warning', 'You must be loggin in.');
            return $this->redirectToRoute('app_login');
        } 
        
        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $text = $commentForm->get('text')->getData();
            if ($forbiddenWordService->isForbidden($text)) {
                $this->addFlash('error', 'Comment contains forbidden words.');
            } else {
                if ($forbiddenWordService->containsForbiddenWord($text)) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("A comment way contain a forbidden word. Please verify.");
                    $adminNotification->setUser(null);
                    $adminNotification->setBlog(null);
                    
                    $comment->setBlog($blog);
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    
                    $adminNotification->setComment($comment);
                    $entityManager->persist($adminNotification);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Comment added.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                } else {
                    $comment->setBlog($blog);
        
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                }
            }
        }
        
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'commentForm' => $commentForm->createView(),
            'comments' => $commentsRepository->findAllOrderedByLatest($blog->getId()),
        ]);
        
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, AdminNotificationRepository $adminNotificationRepo, ForbiddenWordService $forbiddenWordService): Response
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
                $underInvestigation = $adminNotificationRepo->findOneBy(['blog' => ($blog)]);
                if ($underInvestigation) {
                    $entityManager->remove($underInvestigation);
                }
                $title = $form->get('title')->getData();
                $text = $form->get('text')->getData();
                if ($forbiddenWordService->isForbidden($title) || $forbiddenWordService->isForbidden($text)) {
                    $this->addFlash('error', 'Blog contains forbidden words.');
                } else {
                    if ($forbiddenWordService->containsForbiddenWord($title) || $forbiddenWordService->containsForbiddenWord($text)) {
                        $adminNotification = new AdminNotification();
                        $adminNotification->setCreatedAt(now());
                        $message = "";
                        if ($forbiddenWordService->containsForbiddenWord($title) && $forbiddenWordService->containsForbiddenWord($text)) {
                            $message = "A blog's title and text may contain forbidden words. Please verity";
                        } elseif ($forbiddenWordService->containsForbiddenWord($text)) {
                            $message = "A blog's text may contain a forbidden word. Please verify.";
                        } else {
                            $message = "A blog title may contain a forbidden word. Please verify.";
                        }
                        $adminNotification->setText($message);
                        $adminNotification->setUser(null);
                        $adminNotification->setComment(null);
                    
                        $entityManager->persist($blog);
                        $entityManager->flush();
                    
                        $adminNotification->setBlog($blog);
                        $entityManager->persist($adminNotification);
                        $entityManager->flush();
                    
                        $this->addFlash('success', 'Blog updated.');
                        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                    } else {
                        $entityManager->persist($blog);
                        $entityManager->flush();
                        $this->addFlash('success', 'Blog updated.');
                        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                    }
                }
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

    
    #[Route('/{id}/like', name: 'app_blog_like', methods: ['POST'])]
    public function like(Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new AccessDeniedException();
        }

        if ($blog->isLikedByUser($user)) {
        $like = $blog->getLikes()->filter(function (Likes $like) use ($user) {
            return $like->getUserId() === $user;
        })->first();
        
        $entityManager->remove($like);
    } else {
        $like = new Likes();
        $like->setUserId($user);
        $like->setBlogId($blog);
        
        $entityManager->persist($like);
    }
    
    $entityManager->flush();
    
    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
    }
    
    #[Route('/{id}/delete', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
    if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
        $entityManager->remove($blog);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_blog_mine', [], Response::HTTP_SEE_OTHER);
    }
}
