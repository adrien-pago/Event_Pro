<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class GoogleConnectController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        // Redirige vers Google avec des scopes minimaux pour éviter ModSecurity
        return $clientRegistry
            ->getClient('google') // Clé à définir dans un fichier de config
            ->redirect([
                'email', 
                'openid'
            ], [
                'prompt' => 'consent select_account', // Force le consentement ET la sélection
                'access_type' => 'offline' // Demande le refresh_token
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(): void
    {
        // Cette route ne sera jamais exécutée directement.
        // Le firewall de Symfony interceptera la requête et gérera l'authentification.
    }

    #[Route('/google/disconnect', name: 'google_disconnect')]
    public function disconnect(EntityManagerInterface $em): RedirectResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour effectuer cette action.');
            return $this->redirectToRoute('app_login');
        }

        $user->setGoogleId(null);
        $user->setGoogleRefreshToken(null);

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Votre compte Google a été déconnecté avec succès.');

        return $this->redirectToRoute('app_account');
    }
} 