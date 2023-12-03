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
    private $blogRepository;
    private $userRepository;
    private $commentsRepository;
    private $adminNotificationRepository;
    private $forbiddenWordsRepository;
    private $blacklistRepository;
    private $entityManager;

    public function __construct(
        BlogRepository $blogRepository,
        UserRepository $userRepository,
        CommentsRepository $commentsRepository,
        AdminNotificationRepository $adminNotificationRepository,
        ForbiddenWordsRepository $forbiddenWordsRepository,
        BlacklistRepository $blacklistRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->blogRepository = $blogRepository;
        $this->userRepository = $userRepository;
        $this->commentsRepository = $commentsRepository;
        $this->adminNotificationRepository = $adminNotificationRepository;
        $this->forbiddenWordsRepository = $forbiddenWordsRepository;
        $this->blacklistRepository = $blacklistRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/blogs', name: 'app_admin_blogs', methods: ['GET'])]
    public function getBlogsAdmin(): Response
    {
        $url = 'Ablogs';

        return $this->render('admin/blogs.html.twig', [
            'blogs' => $this->blogRepository->findAllOrderedByLatest(),
            'url' => $url,
        ]);
    }

    #[Route('/load-more-blogs', name: 'admin_more_blogs', methods: ['GET'])]
    public function loadMoreBlogs(Request $request): JsonResponse
    {
        $offset = $request->query->get('offset', 0);
        $blogs = $this->blogRepository->findMoreBlogs($offset);

        $html = $this->renderView('admin/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/blogs/{id}/delete', name: 'app_admin_blog_delete', methods: ['POST'])]
    public function blogDeleteAdmin(Request $request, Blog $blog): Response
    {
    if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
        $this->entityManager->remove($blog);
        $this->entityManager->flush();
    }

    return $this->redirectToRoute('app_admin_blogs', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function getUsersAdmin(): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/user-blogs/{id}', name: 'app_admin_userblogs', methods: ['GET'])]
    public function getUserBlogsAdmin(int $id): Response
    {
        $user = $this->userRepository->find($id);
        return $this->render('admin/user_blogs.html.twig', [
            'blogs' => $this->blogRepository->findBy(['createdBy' => $id]),
            'userName' => $user->getUsername(),
        ]);
    }

    #[Route('/password/{id}', name: 'app_admin_password', methods: ['GET', 'POST'])]
    public function changePasswordAdmin(int $id, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->userRepository->find($id);

        $form = $this->createForm(EditPasswordType::class);
        $form->remove('currentPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $validation = $form->get('confirmPassword')->getData();
            if ($newPassword !== $validation) {
                $this->addFlash('error', '*Passwords do not match');
            } else {
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPasswordHash($hashedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', '*Password updated successfully');

            return $this->redirectToRoute('app_blog_index');
        }}
        
        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comments', name: 'app_admin_comments', methods: ['GET'])]
    public function getCommentsAdmin(): Response
    {
        return $this->render('admin/comments.html.twig', [
            'comments' => $this->commentsRepository->findAll(),
        ]);
    }

    #[Route('/comments/{id}', name: 'app_admin_comment', methods: ['GET'])]
    public function commentShowAdmin(Comments $comment): Response
    {
        return $this->render('admin/comment.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/comments/{id}/delete', name: 'app_admin_comments_delete', methods: ['POST'])]
    public function commentDeleteAdmin(Request $request, Comments $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_comments', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/notifications', name: 'app_admin_notifications', methods: ['GET'])]
    public function notificationShowAdmin(): Response
    {
        return $this->render('admin/notifications.html.twig', [
            'admin_notifications' => $this->adminNotificationRepository->findAll(),
        ]);
    }

    #[Route('/notification/{id}/delete', name: 'app_admin_notifications_delete', methods: ['POST'])]
    public function notificationDeleteAdmin(Request $request, AdminNotification $adminNotification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminNotification->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($adminNotification);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_notifications', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/forbidden', name: 'app_admin_forbidden_words', methods: ['GET'])]
    public function forbiddenWordsShowAdmin(): Response
    {
        return $this->render('admin/forbidden.html.twig', [
            'forbidden_words' => $this->forbiddenWordsRepository->findAll(),
        ]);
    }

    #[Route('/forbidden/{id}/delete', name: 'app_admin_forbidden_delete', methods: ['POST'])]
    public function forbiddenWordsDeleteAdmin(Request $request, ForbiddenWords $forbiddenWord): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forbiddenWord->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($forbiddenWord);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_forbidden_words', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/forbidden/new', name: 'app_admin_forbidden_new', methods: ['GET', 'POST'])]
    public function forbiddenWordsNewAdmin(Request $request): Response
    {
        $forbiddenWord = new ForbiddenWords();
        $form = $this->createForm(ForbiddenWordsType::class, $forbiddenWord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $words = explode(',', $forbiddenWord->getWords());

            foreach ($words as $word) {
                $word = trim($word);
                if ($word !== '') {
                    $existingWord = $this->entityManager->getRepository(ForbiddenWords::class)->findOneBy(['words' => $word]);
                    if (!$existingWord) {
                    $forbiddenWordClone = clone $forbiddenWord;
                    $forbiddenWordClone->setWords($word);
                    $this->entityManager->persist($forbiddenWordClone);
                    }
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_forbidden_words', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/forbidden_new.html.twig', [
            'forbidden_word' => $forbiddenWord,
            'form' => $form,
        ]);
    }

    #[Route('/blacklist', name: 'app_admin_blacklist', methods: ['GET'])]
    public function blacklistShowAdmin(): Response
    {
        return $this->render('admin/blacklist.html.twig', [
            'blacklists' => $this->blacklistRepository->findAll(),
        ]);
    }

    #[Route('/blacklist/new', name: 'app_admin_blacklist_new', methods: ['GET', 'POST'])]
    public function blacklistNewAdmin(Request $request): Response
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
                    $existingWord = $this->entityManager->getRepository(Blacklist::class)->findOneBy(['emailAddress' => $email]);
                    if (!$existingWord) {
                    $emailClone = clone $blacklist;
                    $emailClone->setEmailAddress($email);
                    $this->entityManager->persist($emailClone);
                    }
                }
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_blacklist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blacklist_new.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/blacklist/{id}/delete', name: 'app_admin_blacklist_delete', methods: ['POST'])]
    public function blacklistDeleteAdmin(Request $request, Blacklist $blacklist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blacklist->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($blacklist);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_blacklist', [], Response::HTTP_SEE_OTHER);
    }
}

