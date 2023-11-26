<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comments')]
class CommentsController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
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
                $entityManager->flush();
                return $this->redirectToRoute('app_blog_show', ['id' => $comment->getBlog()->getId()], Response::HTTP_SEE_OTHER);
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
