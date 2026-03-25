<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FlightController extends AbstractController
{
    #[Route("/vol/{flightNumber}/{date}", name: "app_flight_show", requirements: ["date" => "\d{4}-\d{2}-\d{2}"])]
    public function show(string $flightNumber, string $date, FlightRepository $flightRepository): Response
    {
        $dateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if (!$dateObj) {
            throw new NotFoundHttpException('Date invalide.');
        }

        $flight = $flightRepository->findByFlightNumberAndDate($flightNumber, $dateObj);
        if (!$flight) {
            throw $this->createNotFoundException('Vol introuvable.');
        }

        $reviews = $flight->getReviews();
        $reviewCount = count($reviews);

        $avgRating = 0;
        $avgComfort = 0;
        $avgPunctuality = 0;
        $avgStaff = 0;
        $avgFood = 0;

        if ($reviewCount > 0) {
            $sumRating = 0;
            $sumComfort = 0;
            $countComfort = 0;
            $sumPunctuality = 0;
            $countPunctuality = 0;
            $sumStaff = 0;
            $countStaff = 0;
            $sumFood = 0;
            $countFood = 0;

            foreach ($reviews as $review) {
                $sumRating += $review->getRating();
                if ($review->getRatingComfort() !== null) {
                    $sumComfort += $review->getRatingComfort();
                    $countComfort++;
                }
                if ($review->getRatingPunctuality() !== null) {
                    $sumPunctuality += $review->getRatingPunctuality();
                    $countPunctuality++;
                }
                if ($review->getRatingStaff() !== null) {
                    $sumStaff += $review->getRatingStaff();
                    $countStaff++;
                }
                if ($review->getRatingFood() !== null) {
                    $sumFood += $review->getRatingFood();
                    $countFood++;
                }
            }

            $avgRating = round($sumRating / $reviewCount, 1);
            $avgComfort = $countComfort > 0 ? round($sumComfort / $countComfort, 1) : null;
            $avgPunctuality = $countPunctuality > 0 ? round($sumPunctuality / $countPunctuality, 1) : null;
            $avgStaff = $countStaff > 0 ? round($sumStaff / $countStaff, 1) : null;
            $avgFood = $countFood > 0 ? round($sumFood / $countFood, 1) : null;
        }

        return $this->render('flight/show.html.twig', [
            'flight' => $flight,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating,
            'avgComfort' => $avgComfort,
            'avgPunctuality' => $avgPunctuality,
            'avgStaff' => $avgStaff,
            'avgFood' => $avgFood,
            'skyscannerPartnerId' => $this->getParameter('app.skyscanner_partner_id'),
        ]);
    }
}
