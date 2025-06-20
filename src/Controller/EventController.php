<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EventFormType;
use App\Service\GoogleCalendarService;

#[Route('/events')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $queryBuilder = $eventRepository->findByUserQueryBuilder($user);

        $events = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // 10 événements par page
        );

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, GoogleCalendarService $calendarService): Response
    {
        $event = new Event();
        $event->setUser($this->getUser());

        $calendars = [];
        // On récupère les calendriers seulement si l'utilisateur est connecté à Google
        if ($this->getUser() && $this->getUser()->getGoogleId()) {
            try {
                $calendars = $calendarService->getCalendars();
            } catch (\Exception $e) {
                // On peut ajouter un message flash si on veut informer l'utilisateur de l'échec
                $this->addFlash('warning', 'Impossible de récupérer les calendriers Google pour le moment.');
            }
        }

        $form = $this->createForm(EventFormType::class, $event, [
            'google_calendars' => $calendars,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On vérifie si un calendrier a été sélectionné pour la synchronisation
            if ($form->has('googleCalendarId')) {
                $calendarId = $form->get('googleCalendarId')->getData();

                if ($calendarId) {
                    try {
                        $googleEventId = $calendarService->addEvent($calendarId, $event);
                        if ($googleEventId) {
                            $event->setGoogleCalendarEventId($googleEventId);
                            $this->addFlash('success', 'Événement synchronisé avec Google Calendar !');
                        } else {
                            $this->addFlash('warning', 'L\'événement a été créé, mais la synchronisation avec Google a échoué.');
                        }
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Erreur critique lors de la synchronisation avec Google : ' . $e->getMessage());
                    }
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('app_events');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
            'is_google_connected' => !empty($calendars)
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, GoogleCalendarService $calendarService): Response
    {
        if ($event->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet événement.');
        }

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'événement est synchronisé, on le met à jour sur Google
            if ($event->getGoogleCalendarEventId()) {
                try {
                    // Note : L'API Google nécessite un ID de calendrier. 'primary' est un alias
                    // pour le calendrier principal de l'utilisateur.
                    $calendarService->updateEvent('primary', $event->getGoogleCalendarEventId(), $event);
                    $this->addFlash('success', 'Événement mis à jour localement et sur Google Calendar.');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'La mise à jour sur Google Calendar a échoué : ' . $e->getMessage());
                }
            } else {
                 $this->addFlash('success', 'Événement modifié avec succès !');
            }
            
            $event->setUpdatedAt(new \DateTime());
            $entityManager->flush();
            return $this->redirectToRoute('app_events');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager, GoogleCalendarService $calendarService): Response
    {
        if ($event->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet événement.');
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            
            // Si l'événement est synchronisé, on le supprime aussi de Google
            if ($event->getGoogleCalendarEventId()) {
                try {
                    $calendarService->deleteEvent('primary', $event->getGoogleCalendarEventId());
                    $this->addFlash('info', 'Événement supprimé de votre Google Calendar.');
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'L\'événement a été supprimé localement, mais la suppression sur Google Calendar a échoué. Vous devrez peut-être le supprimer manuellement.');
                }
            }
            
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Erreur de sécurité lors de la suppression.');
        }

        return $this->redirectToRoute('app_events');
    }
} 