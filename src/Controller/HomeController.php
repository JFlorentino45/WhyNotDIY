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
    private $blogRepository;
    private $categoryRepository;

    public function __construct(
        BlogRepository $blogRepository,
        CategoriesRepository $categoryRepository
    ) {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(): Response
    {
        $url = 'home';
        $categories = $this->categoryRepository->findAll();
        $blogs = $this->blogRepository->findAllOrderedByLatest();

        return $this->render('home/index.html.twig', [
            'blogs' => $blogs,
            'url' => $url,
            'categories' => $categories,
        ]);
    }

    #[Route('/load-more-blogs', name: 'load_more_blogs', methods: ['GET'])]
    public function loadMoreBlogs(Request $request): JsonResponse
    {
        $offset = $request->query->get('offset');
        $blogs = $this->blogRepository->findMoreBlogs($offset);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/search-blogs', name: 'search_blogs', methods: ['GET'])]
    public function searchBlogs(Request $request): JsonResponse
    {
        $searchTerm = $request->query->get('term');
        $blogs = $this->blogRepository->searchBlogs($searchTerm);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }
}
