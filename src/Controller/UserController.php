<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\AdminNotification;
use App\Service\BlacklistService;
use App\Service\ForbiddenWordService;
use App\Repository\AdminNotificationRepository;
use App\Form\UserType;
use function Symfony\Component\Clock\now;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    private $forbiddenWordService;
    private $blacklistService;
    private $entityManager;
    private $adminNotificationRepository;
    private $tokenStorage;
    private $auth;

    public function __construct(
        ForbiddenWordService $forbiddenWordService,
        EntityManagerInterface $entityManager,
        BlacklistService $blacklistService,
        AdminNotificationRepository $adminNotificationRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $auth,
    ) {
        $this->forbiddenWordService = $forbiddenWordService;
        $this->entityManager = $entityManager;
        $this->blacklistService = $blacklistService;
        $this->adminNotificationRepository = $adminNotificationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->auth = $auth;
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $this->getUser()) {
                throw new AccessDeniedException();
            }
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $this->getUser()) {
                throw new AccessDeniedException();
            }
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $oldData = clone $user;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->isModified($oldData)) {
                $email = $form->get('emailAddress')->getData();
                if ($this->blacklistService->isBanned($email)) {
                $this->addFlash('error', '*This E-mail is banned.');
                } else {
                $underInvestigation = $this->adminNotificationRepository->findOneBy(['user' => ($user)]);
                if ($underInvestigation) {
                    $this->entityManager->remove($underInvestigation);
                }
                $username = $form->get('userName')->getData();
                if ($this->forbiddenWordService->isForbidden($username)) {
                    $this->addFlash('error', '*Username contains forbidden words.');
                } else {
                    $service = $this->forbiddenWordService->containsForbiddenWord($username);
                    if ($service['found']) {
                        $adminNotification = new AdminNotification();
                        $adminNotification->setCreatedAt(now());
                        $adminNotification->setText("$username may have a forbidden word in their username. Please verify.");
                        $adminNotification->setBlog(null);
                        $adminNotification->setWords($service['word']);
                        $adminNotification->setComment(null);
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        $adminNotification->setUser($user);
                        $this->entityManager->persist($adminNotification);
                        $this->entityManager->flush();
                        
                        $this->addFlash('success', '*Profile Updated.');

                        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
                    } else {
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        $this->addFlash('success', '*Profile Updated.');
                        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
                    }
                }
                }
            } else {
                $this->addFlash('warning', '*No changes detected.');
                return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }}

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if (!$this->isGranted('ROLE_admin')) {
            if ($user !== $this->getUser()) {
                throw new AccessDeniedException();
            }
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        if ($this->auth->isGranted('ROLE_admin')) {
            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->tokenStorage->setToken(null);
            $this->addFlash('error', '*Sorry to see you go, hope you return soon');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
    }
}
