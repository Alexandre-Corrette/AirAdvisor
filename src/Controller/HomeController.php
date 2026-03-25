<?php

namespace App\Controller;

use App\Repository\AirlineRepository;
use App\Repository\FlightRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route("/", name: "app_home")]
    public function index(ReviewRepository $reviewRepository, AirlineRepository $airlineRepository, FlightRepository $flightRepository): Response
    {
        return $this->render("home/index.html.twig", [
            'latestReviews' => $reviewRepository->findLatestWithRelations(6),
            'topAirlines' => $airlineRepository->findTopByReviewCount(5),
            'totalReviews' => $reviewRepository->count([]),
            'totalFlights' => $flightRepository->count([]),
            'totalAirlines' => $airlineRepository->count([]),
        ]);
    }
}
