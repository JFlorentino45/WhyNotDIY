<?php

namespace App\Controller;

use App\Entity\AdminNotification;
use App\Entity\Comments;
use App\Repository\AdminNotificationRepository;
use App\Form\CommentType;
use App\Service\ForbiddenWordService;
use function Symfony\Component\Clock\now;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/comments')]
class CommentsController extends AbstractController
{
    private $blogRepository;
    private $commentsRepository;
    private $adminNotificationRepository;
    private $forbiddenWordService;
    private $entityManager;
    private $security;

    public function __construct(
        AdminNotificationRepository $adminNotificationRepository,
        ForbiddenWordService $forbiddenWordService,
        EntityManagerInterface $entityManager,
    ) {
        $this->adminNotificationRepository = $adminNotificationRepository;
        $this->forbiddenWordService = $forbiddenWordService;
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $comment->getCreatedBy()) {
                throw new AccessDeniedException();
            }
        }

        $form = $this->createForm(CommentType::class, $comment);
        $oldData = clone $comment;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($comment->isModified($oldData)) {
                $underInvestigation = $this->adminNotificationRepository->findOneBy(['Comment' => ($comment)]);
                if ($underInvestigation) {
                    $this->entityManager->remove($underInvestigation);
                }
                $text = $form->get('text')->getData();
            if ($this->forbiddenWordService->isForbidden($text)) {
                $this->addFlash('error', '*Comment contains forbidden words.');
            } else {
                $service = $this->forbiddenWordService->containsForbiddenWord($text);
                if ($service['found']) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("A comment may contain a forbidden word. Please verify.");
                    $adminNotification->setUser(null);
                    $adminNotification->setBlog(null);
                    $adminNotification->setWords($service['word']);
                    
                    $this->entityManager->persist($comment);
                    $this->entityManager->flush();
                    
                    $adminNotification->setComment($comment);
                    $this->entityManager->persist($adminNotification);
                    $this->entityManager->flush();
                    
                    $this->addFlash('success', '*Comment updated.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()]);
                } else {
        
                    $this->entityManager->persist($comment);
                    $this->entityManager->flush();

                    $this->addFlash('success', '*Comment updated.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()]);
                }
            }
            } else {
                $this->addFlash('warning', '*No changes detected.');
                return $this->redirectToRoute('app_comments_edit', ['id' => $comment->getId()]);
            }
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'commentForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $comment->getCreatedBy()) {
                throw new AccessDeniedException();
            }
        }
        
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()], Response::HTTP_SEE_OTHER);
    }
}
