<?php

namespace App\Controller;

use App\Entity\AdminNotification;
use App\Form\AdminNotificationType;
use App\Repository\AdminNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/notification')]
class AdminNotificationController extends AbstractController
{
    #[Route('/', name: 'app_admin_notification_index', methods: ['GET'])]
    public function index(AdminNotificationRepository $adminNotificationRepository): Response
    {
        return $this->render('admin_notification/index.html.twig', [
            'admin_notifications' => $adminNotificationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adminNotification = new AdminNotification();
        $form = $this->createForm(AdminNotificationType::class, $adminNotification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adminNotification);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_notification/new.html.twig', [
            'admin_notification' => $adminNotification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_notification_show', methods: ['GET'])]
    public function show(AdminNotification $adminNotification): Response
    {
        return $this->render('admin_notification/show.html.twig', [
            'admin_notification' => $adminNotification,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_notification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminNotification $adminNotification, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminNotificationType::class, $adminNotification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_notification/edit.html.twig', [
            'admin_notification' => $adminNotification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_notification_delete', methods: ['POST'])]
    public function delete(Request $request, AdminNotification $adminNotification, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminNotification->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adminNotification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_notification_index', [], Response::HTTP_SEE_OTHER);
    }
}
