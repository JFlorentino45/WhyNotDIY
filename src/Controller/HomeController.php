<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\BlogRepository;



class HomeController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        $url = 'home';

        return $this->render('home/index.html.twig', [
            'blogs' => $blogRepository->findAllOrderedByLatest(),
            'url' => $url,
        ]);
    }

    #[Route('/load-more-blogs', name: 'load_more_blogs', methods: ['GET'])]
    public function loadMoreBlogs(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $offset = $request->query->get('offset', 0);
        $blogs = $blogRepository->findMoreBlogs($offset);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/search-blogs', name: 'search_blogs', methods: ['GET'])]
    public function searchBlogs(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $searchTerm = $request->query->get('term');
        $blogs = $blogRepository->searchBlogs($searchTerm);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }
}
