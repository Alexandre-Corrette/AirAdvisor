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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    * @Route("/results/{departureCity}/{arrivalCity}", name="results", methods={"GET"})
    */
    public function listResults(SearchJourneyService $search, $departureCity, $arrivalCity, FlightRepository $flightRepository): Response
    {   
        $search->departureCity = $departureCity;
        $search->arrivalCity = $arrivalCity;
        $results = $flightRepository->search($search);
        //dd($results);
        return $this->render('search/index.html.twig', 
            [
            'results' => $results,
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
                [
                'departureCity' => $_GET['search_journey_by_flight_number']['departureCity'],
                'arrivalCity' => $_GET['search_journey_by_flight_number']['arrivalCity'],
                'flightDate' => $_GET['search_journey_by_flight_number']['flightDate'],
                'flightNumber' => $_GET['search_journey_by_flight_number']['flightNumber'],

            ]);
              
            }
        }
        return $this->render('search/new.html.twig', [
            
            'searchForm' => $form->createView(),
            'website' => 'FlightAdvisor',
        ]);
    }

    /**
     * @Route("/result/flight/departure/{departureCity}/arrival/{arrivalCity}/date/{flightDate}/flight/{flightNumber}", name="result_one_flight")
     */
    public function showResultOneFlight(Request $request, CallApiService $callApiService,string $flightDate,string $departureCity,string $arrivalCity, FlightRepository $flightRepository, int $flightNumber): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $date = new DateTime($flightDate);
        
        $flightDate = $date->format('Y-m-d');
        $flight = $flightRepository->findOneBy([
            'flightDate' => $date, 
            'departureCity' => $departureCity,
            'arrivalCity' => $arrivalCity,
            'flightNumber' => $flightNumber,
            ]);
        if(empty($flight))
        {   $flight = new Flight();
            $callApiService->setFlightData($flightDate,substr($departureCity, -3),substr($arrivalCity, -3),$flightNumber);
            $flights = $callApiService->callApitHistoricFlights();

            //if flight is not in db, set a new flight
            foreach($flights as $flightData) 
            {   
                $flight->setFlightDate($date)
                        ->setDepartureCity($departureCity)
                        ->setArrivalCity($arrivalCity)
                        ->setFlightNumber($flightNumber)
                        //->setScheduledTime($flightData['departure']['scheduledTime'])
                        //->setArrivalScheduledTime($flightData['arrival']['scheduledTime'])
                        ->setFlightIataCode($flightData['flight']['iataNumber'])
                        ->setAirlineIataCode($flightData['airline']['iataCode'])
                        ->setAirlineName($flightData['airline']['name']);
                        $entityManager->persist($flight);
                        $entityManager->flush();
            }
        }
        
        
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $comment = new Comment();
            $comment->setComment($_POST['comment']['comment'])
                    ->setTitre($_POST['comment']['titre'])
                    ->setRate($_POST['comment']['rate'])
                    ->setAuthor($this->getUser())
                    ->setFlight($flight);
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    return $this->redirectToRoute('comment_list_comments_about_one_flight',
                    ['flightId' => $flight->getId()]);
        } 

    return $this->render('search/results_one_flight.html.twig',
    ['flights' => $flight,
    'error'=> $error = 'il n\'y a pas de vol ',
    'form' => $form->createView(),
    'website' => 'FlightAdvisor',
    ]);

    }
}