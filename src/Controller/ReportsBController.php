<?php

namespace App\Controller;

use App\Entity\ReportsB;
use App\Form\ReportsBType;
use App\Repository\ReportsBRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reports/b')]
class ReportsBController extends AbstractController
{
    #[Route('/', name: 'app_reports_b_index', methods: ['GET'])]
    public function index(ReportsBRepository $reportsBRepository): Response
    {
        return $this->render('reports_b/index.html.twig', [
            'reports_bs' => $reportsBRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reports_b_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reportsB = new ReportsB();
        $form = $this->createForm(ReportsBType::class, $reportsB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reportsB);
            $entityManager->flush();

            return $this->redirectToRoute('app_reports_b_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reports_b/new.html.twig', [
            'reports_b' => $reportsB,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reports_b_show', methods: ['GET'])]
    public function show(ReportsB $reportsB): Response
    {
        return $this->render('reports_b/show.html.twig', [
            'reports_b' => $reportsB,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reports_b_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReportsB $reportsB, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportsBType::class, $reportsB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reports_b_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reports_b/edit.html.twig', [
            'reports_b' => $reportsB,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reports_b_delete', methods: ['POST'])]
    public function delete(Request $request, ReportsB $reportsB, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reportsB->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reportsB);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reports_b_index', [], Response::HTTP_SEE_OTHER);
    }
}
