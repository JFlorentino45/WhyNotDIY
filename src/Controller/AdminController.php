<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\ForbiddenWords;
use App\Entity\Blacklist;
use App\Entity\Blog;
use App\Entity\AdminNotification;
use App\Form\BlacklistType;
use App\Form\EditPasswordType;
use App\Repository\ForbiddenWordsRepository;
use App\Repository\CommentsRepository;
use App\Repository\AdminNotificationRepository;
use App\Repository\BlacklistRepository;
use App\Repository\UserRepository;
use App\Repository\BlogRepository;
use App\Form\ForbiddenWordsType;
use function Symfony\Component\Clock\now;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/blogs', name: 'app_admin_blogs', methods: ['GET'])]
    public function getABlogs(BlogRepository $blogRepository): Response
    {
        return $this->render('admin/blogs.html.twig', [
            'blogs' => $blogRepository->findAllOrderedByLatest(),
        ]);
    }

    #[Route('/load-more-blogs', name: 'admin_more_blogs', methods: ['GET'])]
    public function loadMoreBlogs(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $offset = $request->query->get('offset', 0);
        $blogs = $blogRepository->findMoreBlogs($offset);

        $html = $this->renderView('admin/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/blogs/{id}/delete', name: 'app_admin_blog_delete', methods: ['POST'])]
    public function blogDelete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
    if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
        $entityManager->remove($blog);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_admin_blogs', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function getAUsers(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user-blogs/{id}', name: 'app_admin_userblogs')]
    public function getUserBlogs($id, UserRepository $userRepository, BlogRepository $blogRepository): Response
    {
        $user = $userRepository->find($id);
        return $this->render('admin/user_blogs.html.twig', [
            'blogs' => $blogRepository->findBy(['createdBy' => $id]),
            'userName' => $user->getUsername(),
        ]);
    }

    #[Route('/password/{id}', name: 'app_admin_password')]
    public function changePassword($id, UserRepository $userRepository, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        $form = $this->createForm(EditPasswordType::class);
        $form->remove('currentPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $validation = $form->get('confirmPassword')->getData();
            if ($newPassword !== $validation) {
                $this->addFlash('error', 'Passwords do not match');
            } else {
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPasswordHash($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password updated successfully');

            return $this->redirectToRoute('app_blog_index');
        }}
        
        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comments', name: 'app_admin_comments', methods: ['GET'])]
    public function getComments(CommentsRepository $commentsRepository): Response
    {
        return $this->render('admin/comments.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

    #[Route('/comments/{id}', name: 'app_admin_comment', methods: ['GET'])]
    public function commentShow(Comments $comment): Response
    {
        return $this->render('admin/comment.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/comments/{id}/delete', name: 'app_admin_comments_delete', methods: ['POST'])]
    public function commentDelete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_comments', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/notifications', name: 'app_admin_notifications', methods: ['GET'])]
    public function notifications(AdminNotificationRepository $adminNotificationRepository): Response
    {
        return $this->render('admin/notifications.html.twig', [
            'admin_notifications' => $adminNotificationRepository->findAll(),
        ]);
    }

    #[Route('/notification/{id}/delete', name: 'app_admin_notifications_delete', methods: ['POST'])]
    public function notificationDelete(Request $request, AdminNotification $adminNotification, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminNotification->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adminNotification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_notifications', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/forbidden', name: 'app_admin_forbidden_words', methods: ['GET'])]
    public function forbiddenWords(ForbiddenWordsRepository $forbiddenWordsRepository): Response
    {
        return $this->render('admin/forbidden.html.twig', [
            'forbidden_words' => $forbiddenWordsRepository->findAll(),
        ]);
    }

    #[Route('/forbidden/{id}/delete', name: 'app_admin_forbidden_delete', methods: ['POST'])]
    public function forbiddenDelete(Request $request, ForbiddenWords $forbiddenWord, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forbiddenWord->getId(), $request->request->get('_token'))) {
            $entityManager->remove($forbiddenWord);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_forbidden_words', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/forbidden/new', name: 'app_admin_forbidden_new', methods: ['GET', 'POST'])]
    public function forbiddenNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forbiddenWord = new ForbiddenWords();
        $form = $this->createForm(ForbiddenWordsType::class, $forbiddenWord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $words = explode(',', $forbiddenWord->getWords());

            foreach ($words as $word) {
                $word = trim($word);
                if ($word !== '') {
                    $existingWord = $entityManager->getRepository(ForbiddenWords::class)->findOneBy(['words' => $word]);
                    if (!$existingWord) {
                    $forbiddenWordClone = clone $forbiddenWord;
                    $forbiddenWordClone->setWords($word);
                    $entityManager->persist($forbiddenWordClone);
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_forbidden_words', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/forbidden_new.html.twig', [
            'forbidden_word' => $forbiddenWord,
            'form' => $form,
        ]);
    }

    #[Route('/blacklist', name: 'app_admin_blacklist', methods: ['GET'])]
    public function blacklist(BlacklistRepository $blacklistRepository): Response
    {
        return $this->render('admin/blacklist.html.twig', [
            'blacklists' => $blacklistRepository->findAll(),
        ]);
    }

    #[Route('/blacklist/new', name: 'app_admin_blacklist_new', methods: ['GET', 'POST'])]
    public function blacklistNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blacklist = new Blacklist();
        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);
        $blacklist->setCreatedAt(now());
        if ($form->isSubmitted() && $form->isValid()) {
            $emails = explode(',', $blacklist->getEmailAddress());

            foreach ($emails as $email) {
                $email = trim($email);
                if ($email !== '') {
                    $existingWord = $entityManager->getRepository(Blacklist::class)->findOneBy(['emailAddress' => $email]);
                    if (!$existingWord) {
                    $emailClone = clone $blacklist;
                    $emailClone->setEmailAddress($email);
                    $entityManager->persist($emailClone);
                    }
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_blacklist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blacklist_new.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/blacklist/{id}/delete', name: 'app_admin_blacklist_delete', methods: ['POST'])]
    public function delete(Request $request, Blacklist $blacklist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blacklist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blacklist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_blacklist', [], Response::HTTP_SEE_OTHER);
    }
}

