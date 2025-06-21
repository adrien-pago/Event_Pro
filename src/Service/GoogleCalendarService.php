<?php

namespace App\Service;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class GoogleCalendarService
{
    private $clientRegistry;
    private $security;
    private $entityManager;
    private $logger;

    public function __construct(ClientRegistry $clientRegistry, Security $security, EntityManagerInterface $entityManager, LoggerInterface $logger = null)
    {
        $this->clientRegistry = $clientRegistry;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Récupère un jeton d'accès valide et rafraîchi.
     * @return \League\OAuth2\Client\Token\AccessTokenInterface
     */
    private function getRefreshedAccessToken()
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception("Utilisateur non connecté.");
        }

        if (!$user->getGoogleId()) {
            throw new \Exception("Utilisateur non connecté à Google.");
        }

        if (!$user->getGoogleRefreshToken()) {
            throw new \Exception("Jeton de rafraîchissement manquant. Veuillez vous reconnecter à Google.");
        }

        $this->log("Tentative de rafraîchissement du token pour l'utilisateur: " . $user->getEmail());

        try {
            $client = $this->clientRegistry->getClient('google');
            $provider = $client->getOAuth2Provider();

            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $user->getGoogleRefreshToken()
            ]);

            $this->log("Token rafraîchi avec succès pour: " . $user->getEmail());

            // Google peut parfois renvoyer un nouveau refresh token, mettons-le à jour.
            if ($newAccessToken->getRefreshToken()) {
                $user->setGoogleRefreshToken($newAccessToken->getRefreshToken());
                $this->entityManager->flush();
                $this->log("Nouveau refresh token sauvegardé pour: " . $user->getEmail());
            }

            return $newAccessToken;
        } catch (\Exception $e) {
            $this->log("Erreur lors du rafraîchissement du token: " . $e->getMessage(), 'error');
            throw $e;
        }
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
        try {
            $googleClient = $this->getAuthenticatedClient();
            $accessToken = $this->getRefreshedAccessToken();

            $this->log("Récupération des calendriers pour l'utilisateur: " . $this->security->getUser()->getEmail());

            $request = $googleClient->getAuthenticatedRequest(
                'GET',
                'https://www.googleapis.com/calendar/v3/users/me/calendarList',
                $accessToken
            );

            $response = $googleClient->getParsedResponse($request);
            $calendars = $response['items'] ?? [];
            
            $this->log("Calendriers récupérés avec succès: " . count($calendars) . " calendriers trouvés");
            
            return $calendars;
        } catch (IdentityProviderException $e) {
            $this->log("Erreur IdentityProvider lors de la récupération des calendriers: " . $e->getMessage(), 'error');
            $this->log("Code d'erreur: " . $e->getCode(), 'error');
            
            // Si c'est une erreur d'autorisation, on peut essayer de forcer une nouvelle connexion
            if ($e->getCode() === 401 || $e->getCode() === 403) {
                throw new \Exception("Vos permissions Google Calendar ont expiré. Veuillez vous reconnecter à Google depuis votre compte.");
            }
            
            throw new \Exception("Erreur lors de la récupération des calendriers: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->log("Erreur générale lors de la récupération des calendriers: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    private function buildGoogleEventData(\App\Entity\Event $event): array
    {
        $summary = $event->getEventName();
        $description = 'Événement pour le client : ' . $event->getClientName();

        if ($event->isFullDay()) {
            // Événement sur une journée entière
            $startDate = $event->getEventDate()->format('Y-m-d');
            $endDate = (clone $event->getEventDate())->modify('+1 day')->format('Y-m-d');

            return [
                'summary' => $summary,
                'description' => $description,
                'start' => ['date' => $startDate],
                'end' => ['date' => $endDate],
            ];
        }

        // Événement avec une heure précise
        $startTime = $event->getStartTime();
        $endTime = $event->getEndTime();
        $eventDate = $event->getEventDate();

        if (!$startTime || !$endTime) {
            // Fallback : si les heures ne sont pas définies, crée un événement de 2h
            $startDateTime = (clone $eventDate)->setTime(8, 0); // Commence à 8h par défaut
            $endDateTime = (clone $startDateTime)->modify('+2 hours');
        } else {
            $startDateTime = (clone $eventDate)->setTime(
                (int) $startTime->format('H'),
                (int) $startTime->format('i')
            );

            $endDateTime = (clone $eventDate)->setTime(
                (int) $endTime->format('H'),
                (int) $endTime->format('i')
            );

            // Gère les événements qui se terminent le lendemain
            if ($endDateTime < $startDateTime) {
                $endDateTime->modify('+1 day');
            }
        }

        return [
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $startDateTime->format(\DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                'dateTime' => $endDateTime->format(\DateTime::RFC3339),
                'timeZone' => 'Europe/Paris',
            ],
        ];
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
        try {
            $googleClient = $this->getAuthenticatedClient();
            $accessToken = $this->getRefreshedAccessToken();
            $eventData = $this->buildGoogleEventData($event);
            
            $this->log("Création d'événement Google Calendar: " . $event->getEventName() . " dans le calendrier: " . $calendarId);
            
            $request = $googleClient->getAuthenticatedRequest(
                'POST',
                "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events",
                $accessToken,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($eventData)
                ]
            );

            $response = $googleClient->getParsedResponse($request);
            $eventId = $response['id'] ?? null;
            
            if ($eventId) {
                $this->log("Événement créé avec succès sur Google Calendar. ID: " . $eventId);
            }
            
            return $eventId;
        } catch (IdentityProviderException $e) {
            $this->log("Erreur IdentityProvider lors de la création d'événement: " . $e->getMessage(), 'error');
            return null;
        } catch (\Exception $e) {
            $this->log("Erreur générale lors de la création d'événement: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Met à jour un événement sur Google Calendar.
     */
    public function updateEvent(string $calendarId, string $eventId, \App\Entity\Event $event): ?array
    {
        try {
            $googleClient = $this->getAuthenticatedClient();
            $accessToken = $this->getRefreshedAccessToken();
            $eventData = $this->buildGoogleEventData($event);

            $this->log("Mise à jour d'événement Google Calendar: " . $eventId);

            $request = $googleClient->getAuthenticatedRequest(
                'PUT',
                "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}",
                $accessToken,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($eventData)
                ]
            );

            $response = $googleClient->getParsedResponse($request);
            $this->log("Événement mis à jour avec succès sur Google Calendar");
            return $response;
        } catch (IdentityProviderException $e) {
            $this->log("Erreur IdentityProvider lors de la mise à jour d'événement: " . $e->getMessage(), 'error');
            return null;
        } catch (\Exception $e) {
            $this->log("Erreur générale lors de la mise à jour d'événement: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Supprime un événement de Google Calendar.
     * @return bool true en cas de succès, false sinon.
     */
    public function deleteEvent(string $calendarId, string $eventId): bool
    {
        try {
            $googleClient = $this->getAuthenticatedClient();
            $accessToken = $this->getRefreshedAccessToken();

            $this->log("Suppression d'événement Google Calendar: " . $eventId);

            $request = $googleClient->getAuthenticatedRequest(
                'DELETE',
                "https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}",
                $accessToken
            );

            $response = $googleClient->getResponse($request);
            $success = $response->getStatusCode() === 204;
            
            if ($success) {
                $this->log("Événement supprimé avec succès de Google Calendar");
            }
            
            return $success;
        } catch (IdentityProviderException $e) {
            // Si l'événement a déjà été supprimé sur Google, l'API retourne 410 (Gone)
            if ($e->getCode() === 410) {
                $this->log("Événement déjà supprimé de Google Calendar");
                return true; 
            }
            $this->log("Erreur IdentityProvider lors de la suppression d'événement: " . $e->getMessage(), 'error');
            return false;
        } catch (\Exception $e) {
            $this->log("Erreur générale lors de la suppression d'événement: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Méthode utilitaire pour logger les messages
     */
    private function log(string $message, string $level = 'info'): void
    {
        if ($this->logger) {
            $this->logger->log($level, '[GoogleCalendarService] ' . $message);
        } else {
            // Fallback vers error_log si pas de logger configuré
            error_log('[GoogleCalendarService] ' . $message);
        }
    }
} 