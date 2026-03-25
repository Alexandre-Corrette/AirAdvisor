<?php

namespace App\Controller;

use App\Repository\AirlineRepository;
use App\Repository\FlightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(AirlineRepository $airlineRepository, FlightRepository $flightRepository): Response
    {
        $airlines = $airlineRepository->findAll();
        $flights = $flightRepository->findAll();

        // Extract unique destination cities
        $destinations = [];
        foreach ($flights as $flight) {
            $city = strtolower(str_replace(' ', '-', $flight->getArrivalCity()));
            if (!in_array($city, $destinations, true)) {
                $destinations[] = $city;
            }
        }

        $response = $this->render('sitemap.xml.twig', [
            'airlines' => $airlines,
            'flights' => $flights,
            'destinations' => $destinations,
        ]);

        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
