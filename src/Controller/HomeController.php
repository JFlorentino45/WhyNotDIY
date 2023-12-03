<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\BlogRepository;
use App\Repository\CategoriesRepository;



class HomeController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository, CategoriesRepository $categoryRepository): Response
    {
        $url = 'home';
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'blogs' => $blogRepository->findAllOrderedByLatest(),
            'url' => $url,
            'categories' => $categories,
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

    #[Route('/filter-blogs', name: 'filter_blogs', methods: ['GET'])]
    public function filterBlogs(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $category = $request->query->get('category');
        $searchTerm = $request->query->get('term');
        $blogs = $blogRepository->filterBlogs($category, $searchTerm);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }
}
