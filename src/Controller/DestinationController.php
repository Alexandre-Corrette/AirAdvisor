<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DestinationController extends AbstractController
{
    #[Route('/destination/{city}', name: 'app_destination_show')]
    public function show(string $city, FlightRepository $flightRepository): Response
    {
        $results = $flightRepository->findByDestination($city);

        if (empty($results)) {
            throw $this->createNotFoundException('Aucun vol trouvé vers cette destination.');
        }

        $totalReviews = 0;
        $totalRating = 0;
        $ratedCount = 0;

        foreach ($results as $row) {
            $totalReviews += (int) $row['reviewCount'];
            if ((int) $row['reviewCount'] > 0) {
                $totalRating += (float) $row['avgRating'] * (int) $row['reviewCount'];
                $ratedCount += (int) $row['reviewCount'];
            }
        }

        $avgRating = $ratedCount > 0 ? $totalRating / $ratedCount : 0;
        $cityName = ucfirst(str_replace('-', ' ', $city));

        return $this->render('destination/show.html.twig', [
            'city' => $cityName,
            'citySlug' => $city,
            'flights' => $results,
            'avgRating' => $avgRating,
            'reviewCount' => $totalReviews,
            'skyscannerPartnerId' => $this->getParameter('app.skyscanner_partner_id'),
            'bookingPartnerId' => $this->getParameter('app.booking_partner_id'),
        ]);
    }
}
