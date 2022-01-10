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
     * @Route("/search", name="flight_by_flightNumber")
     */

    public function searchFlightByNumber(Request $request, FlightRepository $flightRepository) {

        $form = $this->createForm(SearchCompanyFlightType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           //check if flight exists in DB
            $flight = $flightRepository->findOneFlightByFlightNumber($_GET['search_company_flight']['flightNumber']);
            if(!empty($flight)) 
            {
                return $this->redirectToRoute('flight_show', 
                    ['flightNumber' => $_GET['search_company_flight']['flightNumber'] ], 
             );
            } else {
                return $this->redirectToRoute('flight_new');
            }
            
           
        }
        return $this->render('search/new.html.twig', [
            
            'form' => $form->createView(),
            'website' => 'AirAdvisor',
        ]);
    }

}