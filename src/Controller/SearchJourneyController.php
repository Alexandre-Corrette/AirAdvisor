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
                'flightDate' => $_GET['search_journey_by_flight_number']['flightDate'] 
            ]);
              
            }
        }
        return $this->render('search/new.html.twig', [
            
            'searchForm' => $form->createView(),
            'website' => 'FlightAdvisor',
        ]);
    }

    /**
     * @Route("/result/flight/departure/{departureCity}/arrival/{arrivalCity}/date/{flightDate}", name="result_one_flight")
     */
    public function showResultOneFlight(Request $request, CallApiService $callApiService, $flightDate, $departureCity, $arrivalCity, FlightRepository $flightRepository): Response
    {   
        //$callApiService->setFlightData($flightDate, substr($departureCity, -3), substr($arrivalCity, -3), $flightNumber);
        $date = new DateTime($flightDate);
        $date->format('Y-m-d');
        
        $comment = new Comment();
        $flight = new Flight();
        //$flight->setFlightNumber($flightNumber);
        $flight->setFlightDate($date);
        $flight->setDepartureCity($departureCity);
        $flight->setArrivalCity($arrivalCity);
        $flights = $callApiService->callApitHistoricFlights($flight);
        dd($flights);
        foreach($flights as $flightData) 
            {
                $flight->setFlightIataCode($flightData['flight']['iataNumber']);
                $flight->setAirlineIataCode($flightData['airline']['iataCode']);
                $flight->setAirlineName($flightData['airline']['name']);
            
            }
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        
        $flightDb = [ 
                    'flightByDeparture' => $flightRepository->findOneBy($departureCity),
                    'flightByArrival' => $flightRepository->findOneBy($arrivalCity),
        ];
        
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
                        $flightDb['flightByDeparture']->addComment($comment);
                        $flightDb['flightByArrival']->addComment($comment);
                        $entityManager->persist($comment);
                        $entityManager->flush();
                    } 
            return $this->redirectToRoute('comment_list_comments_about_one_flight',
                    ['flightId' => $flightDb['flightByDeparture']->getId()]);
        }
        
        return $this->render('search/results_one_flight.html.twig',
        ['flights' => $flights,
        'flightDatas' => $callApiService->getFlightDatas(),
        'error'=> $error = 'il n\'y a pas de vol ',
        'form' => $form->createView(),
        'website' => 'FlightAdvisor',
        ]);
    }

}