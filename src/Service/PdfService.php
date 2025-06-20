<?php

namespace App\Service;

use App\Entity\Event;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generateDevisPdf(Event $event): string
    {
        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true); // Pour charger des images/CSS externes si besoin

        $dompdf = new Dompdf($pdfOptions);

        // Récupérer toutes les prestations, pas seulement celles de la page courante
        $prestations = $event->getPrestations()->toArray();

        // Calculer le total
        $totalPrix = 0;
        foreach ($prestations as $prestation) {
            $totalPrix += $prestation->getPrix();
        }

        // Rendre le template Twig en HTML
        $html = $this->twig->render('pdf/devis_template.html.twig', [
            'event' => $event,
            'prestations' => $prestations,
            'totalPrix' => $totalPrix,
            'currentDate' => new \DateTime()
        ]);

        // Charger l'HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Définir la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Retourner le contenu du PDF généré
        return $dompdf->output();
    }
} 