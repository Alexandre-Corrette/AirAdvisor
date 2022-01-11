<?php

namespace App\Controller;

use DateTime;
use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Service\CallApiService;

use App\Repository\FlightRepository;
use App\Form\SearchCompanyFlightType;
use App\Service\SearchJourneyService;
use Symfony\Component\HttpClient\HttpClient;
use App\Form\SearchJourneyByFlightNumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            if(!empty($_GET)) 
            {   
                //check if flight exists in DB

                
                if(!empty($flightRepository->findOneFlightByFlightNumber($_GET['search_company_flight']['flightNumber']))) {
                    
                    return $this->redirectToRoute('flight_show', 
                    ['flightNumber' => $flightRepository->findOneFlightByFlightNumber($_GET['search_company_flight']['flightNumber']), ]);
                } else {
        
                    return $this->redirectToRoute('search_refine',
                    ['flightNumber' => $_GET['search_company_flight']['flightNumber'] ]
                    
                );
                }
            }
        }
        return $this->render('search/new.html.twig', [
            
            'form' => $form->createView(),
            'website' => 'AirAdvisor',
        ]);
    }

    /**
     * @Route("/search/refine/{flightNumber}", name="refine")
     */
    public function refineSearch(int $flightNumber, CallApiService $callApiService, Request $request): Response
    {   
        $form = $this->createForm(SearchJourneyByFlightNumberType::class);
        $flights = [];
        $error= null; 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!empty($callApiService->setFlightData($_GET['departureDate'], $_GET['departureCity'], $_GET['arrivaCity'], $flightNumber)))
            {
                $flights = $callApiService->callApitHistoricFlights();
            
            }   else 
            {
                $error = 'il n\'y a pas de vol ';
            }

            
        }
        return $this->render('search/refine.html.twig', [
            
            'searchForm' => $form->createView(),
            'website' => 'AirAdvisor',
            'flights' => $flights,
            'error' => $error,
        ]);
    }

}