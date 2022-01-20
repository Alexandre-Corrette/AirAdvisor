<?php

namespace App\Controller;

use DateTime;
use App\Entity\Flight;
use App\Entity\Comment;
use App\Form\CommentType;
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
    public function listResults( CallApiService $callApiService,string $departureCity,string $arrivalCity,string $flightDate, FlightRepository $flightRepository): Response
    {
        
        $departureCityIataCode = substr($departureCity, -3);
        $arrivalCityIataCode = substr($arrivalCity, -3);
        $results = $callApiService->callApiFlights($departureCityIataCode, $arrivalCityIataCode, $flightDate);
        //dd($results);
        foreach($results as $flightData)
            {
                $flightInDb['flight'] = $flightRepository->findOneFlightByFlightNumber($flightData['flight']['number']);
                if(!empty($flightInDb['flight']))
                {
                    $results['comment'] = $flightInDb['flight']->getComments();
                }
                
            }
            dd($results);
        

        
        return $this->render('landing/listresults.html.twig', 
            [
            'results' => $results,
            'departureCity' => $departureCity,
            'arrivalCity' => $arrivalCity,
            'flightDate' => $flightDate,
            'flightsInDb' => $flightInDb,
            'website' => 'AirAdvisor'
            ]
        );
    }

    

    /**
     * @Route("/search", name="flight_by_flightNumber")
     */

    public function searchFlightByNumber(Request $request, FlightRepository $flightRepository) {

        $form = $this->createForm(SearchJourneyByFlightNumberType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if(!empty($_GET)) 
            {   
                return $this->redirectToRoute('search_result_one_flight', 
                ['flightNumber' => $_GET['search_journey_by_flight_number']['flightNumber'],
                'departureCity' => $_GET['search_journey_by_flight_number']['departureCity'],
                'arrivalCity' => $_GET['search_journey_by_flight_number']['arrivalCity'],
                'flightDate' => $_GET['search_journey_by_flight_number']['flightDate'] 
            ]);
              
            }
        }
        return $this->render('search/new.html.twig', [
            
            'searchForm' => $form->createView(),
            'website' => 'AirAdvisor',
        ]);
    }

    /**
     * @Route("/search/refine/{flightNumber<^[0-9]+$>}", name="refine")
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

    /**
     * @Route("/result/flight/{flightNumber<^[0-9]+$>}/departure/{departureCity}/arrival/{arrivalCity}/date/{flightDate}", name="result_one_flight")
     */
    public function showResultOneFlight(Request $request, CallApiService $callApiService, $flightNumber, $flightDate, $departureCity, $arrivalCity, FlightRepository $flightRepository): Response
    {   
        $callApiService->setFlightData($flightDate, substr($departureCity, -3), substr($arrivalCity, -3), $flightNumber);
        $date = new DateTime($flightDate);
        $date->format('Y-m-d');
        
        $comment = new Comment();
        $flight = new Flight();
        $flight->setFlightNumber($flightNumber);
        $flight->setFlightDate($date);
        $flight->setDepartureCity($departureCity);
        $flight->setArrivalCity($arrivalCity);
        $flights = $callApiService->callApitHistoricFlights();
        foreach($flights as $flightData) 
            {
                $flight->setFlightIataCode($flightData['flight']['iataNumber']);
                $flight->setAirlineIataCode($flightData['airline']['iataCode']);
                $flight->setAirlineName($flightData['airline']['name']);
            
            }
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        
        $flightDb = $flightRepository->findOneFlightByFlightNumberAndDate($flightNumber, $date);
        if(!empty($flightDb)) 
        {
            $comments = $flightDb->getComments();
        }
        
        if ($form->isSubmitted() && $form->isValid())
       
            {
                $entityManager = $this->getDoctrine()->getManager();
                $comment->setComment($_POST['comment']['comment']);
                $comment->setTitre($_POST['comment']['titre']);
                $comment->setRate($_POST['comment']['rate']);
                $comment->setAuthor($this->getUser());
                if(empty($flightDb))
                    {
                        $comment->setFlight($flight);
                        $entityManager->persist($comment);
                        $entityManager->persist($flight);
                        $entityManager->flush();
                    } else {
                        $flightDb->addComment($comment);
                        $entityManager->persist($comment);
                        $entityManager->flush();
                    } 
            return $this->redirectToRoute('comment_list_comments_about_one_flight',
                    ['flightId' => $flightDb->getId()]);
        }
        
        return $this->render('search/results_one_flight.html.twig',
        ['flights' => $flights,
        'flightDatas' => $callApiService->getFlightDatas(),
        'comments' => $comments,
        'error'=> $error = 'il n\'y a pas de vol ',
        'form' => $form->createView(),
        'website' => 'FlightAdvisor',
        ]);
    }

}