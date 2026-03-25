<?php

namespace App\Controller;

use App\Repository\AirlineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AirlineController extends AbstractController
{
    #[Route('/compagnies', name: 'app_airline_index')]
    public function index(AirlineRepository $airlineRepository): Response
    {
        $airlines = $airlineRepository->findAllWithStats();

        return $this->render('airline/index.html.twig', [
            'airlines' => $airlines,
        ]);
    }

    #[Route('/compagnie/{slug}', name: 'app_airline_show')]
    public function show(string $slug, AirlineRepository $airlineRepository): Response
    {
        $airline = $airlineRepository->findOneBySlug($slug);

        if (!$airline) {
            throw $this->createNotFoundException('Compagnie introuvable.');
        }

        $stats = $airlineRepository->getAirlineStats($airline);
        $flights = $airlineRepository->findFlightsWithStatsByAirline($airline);

        return $this->render('airline/show.html.twig', [
            'airline' => $airline,
            'flights' => $flights,
            'avgRating' => $stats['avgRating'],
            'reviewCount' => $stats['reviewCount'],
            'skyscannerPartnerId' => $this->getParameter('app.skyscanner_partner_id'),
        ]);
    }
}
