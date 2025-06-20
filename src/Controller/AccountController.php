<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        // On vérifie si l'utilisateur est connecté à Google
        $isGoogleConnected = $this->getUser() ? ($this->getUser()->getGoogleId() !== null) : false;

        return $this->render('account/index.html.twig', [
            'is_google_connected' => $isGoogleConnected,
        ]);
    }

    #[Route('/account/delete', name: 'app_account_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $token)) {
            // Pour déconnecter l'utilisateur, on invalide la session
            $request->getSession()->invalidate();
            $tokenStorage->setToken(null);
            
            // On supprime l'utilisateur de la base de données
            $em->remove($user);
            $em->flush();

            // On ne peut pas mettre de message flash car la session est détruite
            // On redirige vers l'accueil
            return $this->redirectToRoute('app_home');
        }
        
        $this->addFlash('error', 'Jeton de sécurité invalide. Impossible de supprimer le compte.');
        return $this->redirectToRoute('app_account');
    }
} 