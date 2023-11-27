<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\EditPasswordType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Repository\CommentsRepository;
use App\Repository\AdminNotificationRepository;
use App\Repository\BlogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/notifications', name: 'app_admin_notifications', methods: ['GET'])]
    public function index(AdminNotificationRepository $adminNotificationRepository): Response
    {
        return $this->render('admin/notifications.html.twig', [
            'admin_notifications' => $adminNotificationRepository->findAll(),
        ]);
    }
}

