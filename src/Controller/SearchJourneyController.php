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
    * @Route("/results/departureCity/{departureCity}/arrivalCity/{arrivalCity}/flightDate/{flightDate}", name="results", methods={"GET"})
    */
    public function listResults( CallApiService $callApiService,string $departureCity,string $arrivalCity,string $flightDate): Response
    {
        
        $departureCityIataCode = substr($departureCity, -3);
        $arrivalCityIataCode = substr($arrivalCity, -3);
        
        return $this->render('landing/listresults.html.twig', 
            [
            'results' =>$callApiService->callApiFlights($departureCityIataCode, $arrivalCityIataCode, $flightDate),
            'departureCity' => $departureCity,
            'arrivalCity' => $arrivalCity,
            'flightDate' => $flightDate,
            'website' => 'AirAdvisor'
            ]
        );
    }

    /**
     * @Route("/flight/{id}/departure/{iataCode}/date/{flightDate}/flightNumber/{flightNumber}", name="flight")
     */
    public function showFlight($id,$flightDate,$iataCode,$flightNumber, CallApiService $callApiService): Response
    {
    
        $flights = $callApiService->getFlightByFlightNumber($iataCode,$flightDate,$id);
        
        foreach($flights as $flight) {
            if($flightNumber === $flight['flight']['iataNumber'])
            {
                $flightData = $flight;
                
            }
        }
        
        if($flightData['codeshared']['airline']['name']) {
        
            $flightData['pathToLogo'] = file_exists('/Users/alexandrecorrette/www/airadvisor/assets/images/logo-'.str_replace(" ", "", $flightData['codeshared']['airline']['name']).'.png');
            
         
        } else {
            $flightData['pathToLogo'] = file_exists('/Users/alexandrecorrette/www/airadvisor/public/assets/images/logo-'.$flightData['airline']['name'].'.png');
        }
     
        
        return $this->render('flight/show.html.twig',['flight'=> $flightData]);

    }

}