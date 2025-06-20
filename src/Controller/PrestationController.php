<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Prestation;
use App\Form\PrestationFormType;
use App\Repository\PrestationRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event/{event_id}/prestations')]
class PrestationController extends AbstractController
{
    #[Route('/', name: 'app_prestation_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, PrestationRepository $prestationRepository, EntityManagerInterface $entityManager, int $event_id): Response
    {
        $event = $this->getEventFromId($event_id, $entityManager);

        $prestation = new Prestation();
        $form = $this->createForm(PrestationFormType::class, $prestation, [
            'action' => $this->generateUrl('app_prestation_new', ['event_id' => $event->getId()]),
            'method' => 'POST',
        ]);
        
        $queryBuilder = $prestationRepository->findByEventQueryBuilder($event);

        $prestations = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $totals = $prestationRepository->getTotals($event);

        return $this->render('prestation/index.html.twig', [
            'event' => $event,
            'prestations' => $prestations,
            'totalPrix' => $totals['totalPrix'],
            'totalMarge' => $totals['totalMarge'],
            'form' => $form->createView(),
        ]);
    }

    #[Route('/download-devis', name: 'app_prestation_download_devis', methods: ['GET'])]
    public function downloadDevis(int $event_id, EntityManagerInterface $entityManager, PdfService $pdfService): Response
    {
        $event = $this->getEventFromId($event_id, $entityManager);
        
        $pdfContent = $pdfService->generateDevisPdf($event);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="devis-' . $event->getClientName() . '-' . $event->getEventName() . '.pdf"');

        return $response;
    }

    #[Route('/new', name: 'app_prestation_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $event_id): Response
    {
        $event = $this->getEventFromId($event_id, $entityManager);
        
        $prestation = new Prestation();
        $prestation->setEvent($event);
        
        $form = $this->createForm(PrestationFormType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prestation);
            $entityManager->flush();

            $this->addFlash('success', 'La prestation a été ajoutée avec succès.');
        } else {
            // Gérer les erreurs de formulaire si nécessaire
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la prestation.');
        }

        return $this->redirectToRoute('app_prestation_index', ['event_id' => $event->getId()]);
    }

    #[Route('/{prestation_id}/edit', name: 'app_prestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $event_id, int $prestation_id): Response
    {
        $event = $this->getEventFromId($event_id, $entityManager);
        $prestation = $entityManager->getRepository(Prestation::class)->find($prestation_id);

        if (!$prestation || $prestation->getEvent() !== $event) {
            throw $this->createNotFoundException('Prestation non trouvée.');
        }
        
        $form = $this->createForm(PrestationFormType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La prestation a été modifiée avec succès.');
            return $this->redirectToRoute('app_prestation_index', ['event_id' => $event->getId()]);
        }
        
        // Si la requête est en AJAX (pour charger le formulaire dans la modale)
        if ($request->isXmlHttpRequest()) {
            return $this->render('prestation/_edit_form.html.twig', [
                'event' => $event,
                'prestation' => $prestation,
                'form' => $form->createView()
            ]);
        }

        // Fallback pour un affichage non-JS ou une soumission invalide
        // (on pourrait imaginer une page dédiée)
        return $this->render('prestation/edit.html.twig', [
            'event' => $event,
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{prestation_id}/delete', name: 'app_prestation_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $event_id, int $prestation_id): Response
    {
        $event = $this->getEventFromId($event_id, $entityManager);
        $prestation = $entityManager->getRepository(Prestation::class)->find($prestation_id);

        // Vérifie que la prestation existe et appartient bien à l'événement
        if (!$prestation || $prestation->getEvent() !== $event) {
            throw $this->createNotFoundException('Prestation non trouvée.');
        }

        // Vérifie le token CSRF
        if ($this->isCsrfTokenValid('delete'.$prestation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($prestation);
            $entityManager->flush();
            $this->addFlash('success', 'La prestation a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token de sécurité invalide. La suppression a été annulée.');
        }

        return $this->redirectToRoute('app_prestation_index', ['event_id' => $event->getId()]);
    }

    private function getEventFromId(int $eventId, EntityManagerInterface $entityManager): Event
    {
        $event = $entityManager->getRepository(Event::class)->find($eventId);
        if (!$event) {
            throw $this->createNotFoundException("L'événement demandé n'existe pas.");
        }

        if ($this->getUser() !== $event->getUser()) {
            throw $this->createAccessDeniedException("Vous n'avez pas l'autorisation d'accéder à cet événement.");
        }
        
        return $event;
    }
} 