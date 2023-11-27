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
    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager, ForbiddenWordService $forbiddenWordService, AdminNotificationRepository $adminNotificationRepo): Response
    {
        $user = $this->getUser();
        if ($user !== $comment->getCreatedBy() && !$this->isGranted('ROLE_admin')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $oldData = clone $comment;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($comment->isModified($oldData)) {
                $underInvestigation = $adminNotificationRepo->findOneBy(['Comment' => ($comment)]);
                if ($underInvestigation) {
                    $entityManager->remove($underInvestigation);
                }
                $text = $form->get('text')->getData();
            if ($forbiddenWordService->isForbidden($text)) {
                $this->addFlash('error', 'Comment contains forbidden words.');
            } else {
                if ($forbiddenWordService->containsForbiddenWord($text)) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->setCreatedAt(now());
                    $adminNotification->setText("A comment may contain a forbidden word. Please verify.");
                    $adminNotification->setUser(null);
                    $adminNotification->setBlog(null);
                    
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    
                    $adminNotification->setComment($comment);
                    $entityManager->persist($adminNotification);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Comment updated.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()]);
                } else {
        
                    $entityManager->persist($comment);
                    $entityManager->flush();

                    $this->addFlash('success', 'Comment updated.');
                    return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()]);
                }
            }
            } else {
                $this->addFlash('warning', 'No changes detected.');
                return $this->redirectToRoute('app_comments_edit', ['id' => $comment->getId()]);
            }
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'commentForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()], Response::HTTP_SEE_OTHER);
    }
}
