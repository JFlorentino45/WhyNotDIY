<?php

namespace App\Controller;

use App\Entity\ReportsC;
use App\Form\ReportsCType;
use App\Repository\ReportsCRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reports/c')]
class ReportsCController extends AbstractController
{
    #[Route('/', name: 'app_reports_c_index', methods: ['GET'])]
    public function index(ReportsCRepository $reportsCRepository): Response
    {
        return $this->render('reports_c/index.html.twig', [
            'reports_cs' => $reportsCRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reports_c_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reportsC = new ReportsC();
        $form = $this->createForm(ReportsCType::class, $reportsC);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reportsC);
            $entityManager->flush();

            return $this->redirectToRoute('app_reports_c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reports_c/new.html.twig', [
            'reports_c' => $reportsC,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reports_c_show', methods: ['GET'])]
    public function show(ReportsC $reportsC): Response
    {
        return $this->render('reports_c/show.html.twig', [
            'reports_c' => $reportsC,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reports_c_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReportsC $reportsC, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportsCType::class, $reportsC);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reports_c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reports_c/edit.html.twig', [
            'reports_c' => $reportsC,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reports_c_delete', methods: ['POST'])]
    public function delete(Request $request, ReportsC $reportsC, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reportsC->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reportsC);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reports_c_index', [], Response::HTTP_SEE_OTHER);
    }
}
