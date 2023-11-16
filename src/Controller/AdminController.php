<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\BlogRepository;
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
}
