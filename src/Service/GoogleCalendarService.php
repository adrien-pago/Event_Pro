<?php

namespace App\Service;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class GoogleCalendarService
{
    private $clientRegistry;
    private $security;
    private $entityManager;

    public function __construct(ClientRegistry $clientRegistry, Security $security, EntityManagerInterface $entityManager)
    {
        $this->clientRegistry = $clientRegistry;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * Récupère un jeton d'accès valide et rafraîchi.
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     */
    private function getRefreshedAccessToken()
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();

        if (!$user || !$user->getGoogleRefreshToken()) {
            throw new \Exception("Utilisateur non connecté ou jeton de rafraîchissement manquant.");
        }

        $client = $this->clientRegistry->getClient('google');
        $provider = $client->getOAuth2Provider();

        $newAccessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $user->getGoogleRefreshToken()
        ]);

        // Google peut parfois renvoyer un nouveau refresh token, mettons-le à jour.
        if ($newAccessToken->getRefreshToken()) {
            $user->setGoogleRefreshToken($newAccessToken->getRefreshToken());
            $this->entityManager->flush();
        }

        return $newAccessToken;
    }

    /**
     * Récupère le client Google authentifié pour l'utilisateur actuel.
     * @return \League\OAuth2\Client\Provider\Google
     */
    private function getAuthenticatedClient()
    {
        return $this->clientRegistry->getClient('google')->getOAuth2Provider();
    }

    /**
     * Récupère la liste des calendriers de l'utilisateur.
     * @return array
     */
    public function getCalendars(): array
    {
        $googleClient = $this->getAuthenticatedClient();
        $accessToken = $this->getRefreshedAccessToken();

        $request = $googleClient->getAuthenticatedRequest(
            'GET',
            'https://www.googleapis.com/calendar/v3/users/me/calendarList',
            $accessToken
        );

        try {
            $response = $googleClient->getParsedResponse($request);
            // On retire le filtre trop strict pour s'assurer que tous les calendriers apparaissent.
            return $response['items'] ?? [];
        } catch (IdentityProviderException $e) {
            // Gérer l'erreur, par exemple logger ou retourner un tableau vide
            return [];
        }
    }

    /**
     * Ajoute un événement à un calendrier Google.
     *
     * @param string $calendarId
     * @param \App\Entity\Event $event
     * @return string|null L'ID de l'événement créé sur Google Calendar, ou null en cas d'échec.
     */
    public function addEvent(string $calendarId, \App\Entity\Event $event): ?string
    {
        $googleClient = $this->getAuthenticatedClient();
        $accessToken = $this->getRefreshedAccessToken();

        $eventData = [
            'summary' => $event->getEventName(),
            'description' => 'Événement pour le client : ' . $event->getClientName(),
            'start' => [
                'dateTime' => $event->getEventDate()->format(\DateTime::RFC3339),
                'timeZone' => 'Europe/Paris', // Il serait bon de le rendre configurable plus tard
            ],
            'end' => [
                'dateTime' => $event->getEventDate()->modify('+2 hours')->format(\DateTime::RFC3339), // Durée par défaut : 2h
                'timeZone' => 'Europe/Paris',
            ],
        ];
        
        $request = $googleClient->getAuthenticatedRequest(
            'POST',
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events",
            $accessToken,
            [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($eventData)
            ]
        );

        try {
            $response = $googleClient->getParsedResponse($request);
            return $response['id'] ?? null;
        } catch (IdentityProviderException $e) {
            // Loguer l'erreur serait une bonne pratique
            error_log('Google API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Met à jour un événement sur Google Calendar.
     */
    public function updateEvent(string $calendarId, string $eventId, \App\Entity\Event $event): ?array
    {
        $googleClient = $this->getAuthenticatedClient();
        $accessToken = $this->getRefreshedAccessToken();

        $eventData = [
            'summary' => $event->getEventName(),
            'description' => 'Événement pour le client : ' . $event->getClientName(),
            'start' => [
                'dateTime' => $event->getEventDate()->format(\DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                'dateTime' => $event->getEventDate()->modify('+2 hours')->format(\DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
        ];

        $request = $googleClient->getAuthenticatedRequest(
            'PUT',
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}",
            $accessToken,
            [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($eventData)
            ]
        );

        try {
            return $googleClient->getParsedResponse($request);
        } catch (IdentityProviderException $e) {
            error_log('Google API Error on update: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Supprime un événement de Google Calendar.
     * @return bool true en cas de succès, false sinon.
     */
    public function deleteEvent(string $calendarId, string $eventId): bool
    {
        $googleClient = $this->getAuthenticatedClient();
        $accessToken = $this->getRefreshedAccessToken();

        $request = $googleClient->getAuthenticatedRequest(
            'DELETE',
            "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}",
            $accessToken
        );

        try {
            // Une réponse avec un statut 204 (No Content) signifie que la suppression a réussi.
            $response = $googleClient->getResponse($request);
            return $response->getStatusCode() === 204;
        } catch (IdentityProviderException $e) {
            // Si l'événement a déjà été supprimé sur Google, l'API retourne 410 (Gone)
            if ($e->getCode() === 410) {
                return true; 
            }
            error_log('Google API Error on delete: ' . $e->getMessage());
            return false;
        }
    }

    // Nous ajouterons ici les méthodes pour :
    // 1. Lister les calendriers de l'utilisateur
    // 2. Créer un événement
    // 3. Mettre à jour un événement
    // 4. Supprimer un événement
} 