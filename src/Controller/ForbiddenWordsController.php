<?php

namespace App\Controller;

use App\Entity\ForbiddenWords;
use App\Form\ForbiddenWordsType;
use App\Repository\ForbiddenWordsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forbidden/words')]
class ForbiddenWordsController extends AbstractController
{
    #[Route('/', name: 'app_forbidden_words_index', methods: ['GET'])]
    public function index(ForbiddenWordsRepository $forbiddenWordsRepository): Response
    {
        return $this->render('forbidden_words/index.html.twig', [
            'forbidden_words' => $forbiddenWordsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_forbidden_words_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forbiddenWord = new ForbiddenWords();
        $form = $this->createForm(ForbiddenWordsType::class, $forbiddenWord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forbiddenWord);
            $entityManager->flush();

            return $this->redirectToRoute('app_forbidden_words_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forbidden_words/new.html.twig', [
            'forbidden_word' => $forbiddenWord,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forbidden_words_show', methods: ['GET'])]
    public function show(ForbiddenWords $forbiddenWord): Response
    {
        return $this->render('forbidden_words/show.html.twig', [
            'forbidden_word' => $forbiddenWord,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forbidden_words_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForbiddenWords $forbiddenWord, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForbiddenWordsType::class, $forbiddenWord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forbidden_words_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forbidden_words/edit.html.twig', [
            'forbidden_word' => $forbiddenWord,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forbidden_words_delete', methods: ['POST'])]
    public function delete(Request $request, ForbiddenWords $forbiddenWord, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forbiddenWord->getId(), $request->request->get('_token'))) {
            $entityManager->remove($forbiddenWord);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forbidden_words_index', [], Response::HTTP_SEE_OTHER);
    }
}
