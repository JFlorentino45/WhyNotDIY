<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Entity\Likes;
use App\Entity\Comments;
use App\Entity\AdminNotification;
use App\Entity\ReportsB;
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

    private $blogRepository;
    private $commentsRepository;
    private $adminNotificationRepository;
    private $forbiddenWordService;
    private $entityManager;
    private $security;

    public function __construct(
        BlogRepository $blogRepository,
        CommentsRepository $commentsRepository,
        AdminNotificationRepository $adminNotificationRepository,
        ForbiddenWordService $forbiddenWordService,
        EntityManagerInterface $entityManager,
        Security $security,
    ) {
        $this->blogRepository = $blogRepository;
        $this->commentsRepository = $commentsRepository;
        $this->adminNotificationRepository = $adminNotificationRepository;
        $this->forbiddenWordService = $forbiddenWordService;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        //used for creating new blogs
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //get title and text data to pass into check
            $title = $form->get('title')->getData();
            $text = $form->get('text')->getData();
            if ($this->forbiddenWordService->isForbidden($title) || $this->forbiddenWordService->isForbidden($text)) {
                //if the title or text contains 'direct' forbidden words, pop up message
                $this->addFlash('error', '*Blog contains forbidden words.');
            } else {
                $serviceText = $this->forbiddenWordService->containsForbiddenWord($text);
                $serviceTitle = $this->forbiddenWordService->containsForbiddenWord($title);

                if ($serviceTitle['found'] || $serviceText['found']) {
                    //if the 'forbidden words' are part of another word, send notification to verfiy
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $message = "";
                    if ($serviceTitle['found'] && $serviceText['found']) {
                        //first both title and text then just text then just title
                        $message = "A blog's title and text may contain forbidden words. Please verify";
                        $word = ['Title: ' . $serviceTitle['word'] . ' Text: ' . $serviceText['word']];
                    } elseif ($serviceText['found']) {
                        $message = "A blog's text may contain a forbidden word. Please verify.";
                        $word = $serviceText['word'];
                    } else {
                        $message = "A blog title may contain a forbidden word. Please verify.";
                        $word = $serviceTitle['word'];
                    }
                    $adminNotification->setText($message);
                    $adminNotification->setWords($word);
                    $adminNotification->setUser(null);
                    $adminNotification->setComment(null);
                    
                    $this->entityManager->persist($blog);
                    $this->entityManager->flush();
                    
                    $adminNotification->setBlog($blog);
                    $this->entityManager->persist($adminNotification);
                    $this->entityManager->flush();
                    
                    $this->addFlash('success', '*Blog created.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                } else {
                    $this->entityManager->persist($blog);
                    $this->entityManager->flush();
                    $this->addFlash('success', '*Blog created.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                }
            }
        }
        
        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }
    
    #[Route('/my-blogs', name: 'app_blog_mine', methods: ['GET'])]
    public function myBlogs(): Response
    {
        $user = $this->security->getUser();
        $url = 'myBlogs';

        return $this->render('blog/myBlogs.html.twig', [
            'blogs' => $this->blogRepository->findMyBlogsOrderedByLatest($user),
            'url' => $url,
        ]);
    }

    #[Route('/load-more-blogs', name: 'app_blog_more', methods: ['GET'])]
    public function loadMoreMyBlogs(Request $request): Response
    {
        $user = $this->security->getUser();
        $offset = $request->query->get('offset');
        $blogs = $this->blogRepository->findMoreMyBlogs($user, $offset);

        $html = $this->renderView('blog/_blog_items.html.twig', ['blogs' => $blogs]);

        return new Response($html);
    }

    #[Route('/user-blogs/{id}', name: 'app_blog_user', methods: ['GET'])]
    public function userBlogs(User $user): Response
    {
        $id = $user->getId();
        $userName = $user->getUserName();
        $url = 'userBlogs';
    
        return $this->render('blog/userBlogs.html.twig', [
            'blogs' => $this->blogRepository->findMyBlogsOrderedByLatest($user),
            'username' => $userName,
            'url' => $url,
            'user' => $id,
        ]);
    }

    #[Route('/load-user-blogs/{id}', name: 'app_user_blogs_more', methods: ['GET'])]
    public function loadUserBlogs(Request $request, User $user): Response
    {
        $id = $user->getId();
        $offset = $request->query->get('offset');
        $blogs = $this->blogRepository->findMoreMyBlogs($id, $offset);

        $html = $this->renderView('blog/_userblog_items.html.twig', ['blogs' => $blogs]);

        return new Response($html);
    }
    
    #[Route('/{id}', name: 'app_blog_show', methods: ['GET', 'POST'])]
    public function show(Blog $blog, Request $request): Response
    {
        
        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $text = $commentForm->get('text')->getData();
            if ($this->forbiddenWordService->isForbidden($text)) {
                $this->addFlash('error', '*Comment contains forbidden words.');
            } else {
                $service = $this->forbiddenWordService->containsForbiddenWord($text);
                if ($service['found']) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("A comment way contain a forbidden word. Please verify.");
                    $adminNotification->setUser(null);
                    $adminNotification->setWords($service['word']);
                    $adminNotification->setBlog(null);
                    
                    $comment->setBlog($blog);
                    $this->entityManager->persist($comment);
                    $this->entityManager->flush();
                    
                    $adminNotification->setComment($comment);
                    $this->entityManager->persist($adminNotification);
                    $this->entityManager->flush();
                    
                    $this->addFlash('success', '*Comment added.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                } else {
                    $comment->setBlog($blog);
        
                    $this->entityManager->persist($comment);
                    $this->entityManager->flush();
                    
                    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                }
            }
        }
        
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'commentForm' => $commentForm->createView(),
            'comments' => $this->commentsRepository->findBlogOrderedByLatest($blog->getId()),
        ]);
        
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $blog->getCreatedBy()) {
                throw new AccessDeniedException();
            }
        }

        $form = $this->createForm(BlogType::class, $blog);
        $oldData = clone $blog;
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($blog->isModified($oldData)) {
                $underInvestigation = $this->adminNotificationRepository->findOneBy(['blog' => ($blog)]);
                if ($underInvestigation) {
                    $this->entityManager->remove($underInvestigation);
                }
                $title = $form->get('title')->getData();
                $text = $form->get('text')->getData();
                if ($this->forbiddenWordService->isForbidden($title) || $this->forbiddenWordService->isForbidden($text)) {
                    $this->addFlash('error', '*Blog contains forbidden words.');
                } else {
                    $serviceText = $this->forbiddenWordService->containsForbiddenWord($text);
                    $serviceTitle = $this->forbiddenWordService->containsForbiddenWord($title);
                    if ($serviceTitle['found'] || $serviceText['found']) {
                        $adminNotification = new AdminNotification();
                        $adminNotification->setCreatedAt(now());
                        $message = "";
                        $word = [];
                        if ($serviceTitle['found'] && $serviceText['found']) {
                            $titleWord = is_array($serviceTitle['word']) ? implode(', ', $serviceTitle['word']) : $serviceTitle['word'];
                            $textWord = is_array($serviceText['word']) ? implode(', ', $serviceText['word']) : $serviceText['word'];
        
                            $message = "A blog's title and text may contain forbidden words. Please verify";
                            $word = ['Title: ' . $titleWord . ' Text: ' . $textWord];
                        } elseif ($serviceText['found']) {
                            $message = "A blog's text may contain a forbidden word. Please verify.";
                            $word = $serviceText['word'];
                        } else {
                            $message = "A blog title may contain a forbidden word. Please verify.";
                            $word = $serviceTitle['word'];
                        }
                        $adminNotification->setText($message);
                        $adminNotification->setUser(null);
                        $adminNotification->setWords($word);
                        $adminNotification->setComment(null);
                    
                        $this->entityManager->persist($blog);
                        $this->entityManager->flush();
                    
                        $adminNotification->setBlog($blog);
                        $this->entityManager->persist($adminNotification);
                        $this->entityManager->flush();
                    
                        $this->addFlash('success', '*Blog updated.');
                        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                    } else {
                        $this->entityManager->persist($blog);
                        $this->entityManager->flush();
                        $this->addFlash('success', '*Blog updated.');
                        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
                    }
                }
            } else {
                $this->addFlash('warning', '*No changes detected.');
                return $this->redirectToRoute('app_blog_edit', ['id' => $blog->getId()]);
            }
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    
    #[Route('/{id}/like', name: 'app_blog_like', methods: ['POST'])]
    public function like(Blog $blog): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        if ($blog->isLikedByUser($user)) {
            $like = $blog->getLikes()->filter(function (Likes $like) use ($user) {
            return $like->getUserId() === $user;
            })->first();
        
            $this->entityManager->remove($like);
        } else {
            $like = new Likes();
            $like->setUserId($user);
            $like->setBlogId($blog);
        
            $this->entityManager->persist($like);
        }
    
        $this->entityManager->flush();
        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);

    }

    #[Route('/{id}/report', name: 'app_blog_report', methods: ['POST'])]
    public function reportBlog(Blog $blog): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new AccessDeniedException();
        }

        if ($blog->isReportedByUser($user)) {
            $this->addFlash('warning', '*Blog already reported.');
            return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()], Response::HTTP_SEE_OTHER);
        } else {
            $report = new ReportsB();
            $report->setReporterId($user);
            $report->setBlogId($blog);
        
            $this->entityManager->persist($report);
        }
    
        $this->entityManager->flush();
        $this->addFlash('warning', '*Blog Reported.');
        return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()], Response::HTTP_SEE_OTHER);

    }
    
    #[Route('/{id}/delete', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog): Response
    {

        $user = $this->getUser();
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $blog->getCreatedBy()) {
                throw new AccessDeniedException();
            }
        }

        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($blog);
            $this->entityManager->flush();
        }

    return $this->redirectToRoute('app_blog_mine', [], Response::HTTP_SEE_OTHER);
    }
}
