<?php

namespace App\Controller;

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
    #[Route('/blogs/{id}', name: 'app_blog_category')]
    public function catBlogs(BlogRepository $blogRepository,int $id, CategoriesRepository $categoryRepository): Response
    {
        $url = 'catBlogs';
        $category = $categoryRepository->find($id);
        $name = $category->getCategory();
        $categories = $categoryRepository->findAll();

        return $this->render('home/categoryIndex.html.twig', [
            'blogs' => $blogRepository->findCategoryOrderedByLatest($id),
            'url' => $url,
            'categories' => $categories,
            'category' => $name,
            'id' => $id,
        ]);
    }

    #[Route('/load-blogs/{id}', name: 'app_blog_category_more')]
    public function loadCatBlogs(BlogRepository $blogRepository, $id, Request $request): JsonResponse
    {

        $offset = $request->query->get('offset', 0);
        $blogs = $blogRepository->findMoreCategoryBlogs($offset, $id);

        $html = $this->renderView('home/_cat_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }

    #[Route('/search-blogs/{id}', name: 'search_cat_blogs', methods: ['GET'])]
    public function searchBlogs(Request $request, BlogRepository $blogRepository, $id): JsonResponse
    {
        $searchTerm = $request->query->get('term');
        $blogs = $blogRepository->searchCatBlogs($searchTerm, $id);

        $html = $this->renderView('home/_blog_items.html.twig', ['blogs' => $blogs]);

        return new JsonResponse(['html' => $html]);
    }
}
