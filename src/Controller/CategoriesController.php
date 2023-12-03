<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\BlogRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }

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
