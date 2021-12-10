<?php

namespace App\Controller;

use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Repository\FlightRepository;
use App\Form\SearchCompanyFlightType;
use App\Service\CallApiService;
use App\Service\SearchJourneyService;
use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @Route("/", name="search_")
 */

class SearchJourneyController extends AbstractController
{
     /**
     * @Route("/results/departueCity/{departureCity}/arrivalCity/{arrivalCity}/flightDate/{flightDate}", name="results", methods={"GET"})
     */
    public function listResults( CallApiService $callApiService,$departureCity, $arrivalCity,$flightDate) {
       
        $data = new SearchJourneyService($departureCity,$arrivalCity,$flightDate);
        //create an airport list
        $airportList = $callApiService->callApiAirports();
        //Get the IataCode for departure & arrival airports
        $data->searchAirportIataCode($airportList);
        //get a list of flights for destination requested
        $data->getFlights($callApiService);
        
        return $this->render('landing/listresults.html.twig', 
            [
            'results' =>$data,
            'website' => 'AirAdvisor'
            ]
        );
    
}

}