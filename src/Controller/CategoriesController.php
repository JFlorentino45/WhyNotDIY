<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\BlogRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoriesController extends AbstractController
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

    #[Route('/blogs/{id}', name: 'app_blog_category')]
    public function catBlogs(Categories $categories): Response
    {
        $url = 'catBlogs';
        $id = $categories->getId();
        $category = $this->categoryRepository->find($id);
        $name = $category->getCategory();
        $categories = $this->categoryRepository->findAll();

        return $this->render('home/categoryIndex.html.twig', [
            'blogs' => $this->blogRepository->findCategoryOrderedByLatest($id),
            'url' => $url,
            'categories' => $categories,
            'category' => $name,
            'id' => $id,
        ]);
    }

    #[Route('/load-blogs/{id}', name: 'app_blog_category_more')]
    public function loadCatBlogs(Categories $categories, Request $request): JsonResponse
    {
        $id = $categories->getId();
        $offset = $request->query->get('offset', 0);
        $blogs = $this->blogRepository->findMoreCategoryBlogs($offset, $id);

        $html = $this->renderView('home/_cat_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/search-blogs/{id}', name: 'search_cat_blogs', methods: ['GET'])]
    public function searchBlogs(Request $request, Categories $categories): JsonResponse
    {
        $id = $categories->getId();
        $searchTerm = $request->query->get('term');
        $blogs = $this->blogRepository->searchCatBlogs($searchTerm, $id);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }
}
