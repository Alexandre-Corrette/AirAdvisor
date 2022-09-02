<?php

namespace App\Controller;

use App\Entity\Flight;
use DateTimeInterface;
use App\Form\FlightType;
use App\Service\SearchJourney;
use App\Service\CallApiService;
use DateTime as GlobalDateTime;
use App\Repository\FlightRepository;
use App\Service\SearchJourneyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/flight", name="flight_")
 */
class FlightController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(FlightRepository $flightRepository ): Response
    {
        return $this->render('flight/index.html.twig', [
            'flights' => $flightRepository->findAll(),
        ]);
    }
     /**
    * @Route("/results/{departureCity}/{arrivalCity}", name="results", methods={"GET"})
    */
    public function listFlights(SearchJourneyService $search, $departureCity, $arrivalCity, FlightRepository $flightRepository): Response
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
     * @route("/)
     */

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, CallApiService $callApi, FlightRepository $flightRepository): Response
    {
        $flight = new Flight();
        $form = $this->createForm(FlightType::class, $flight);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        {  
            
            return $this->redirectToRoute('flight_index', 
               
            );
        }

        return $this->render('flight/new.html.twig', [
            
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/{flightNumber}/d/{iataCode}/id/{id}", name="", methods={"GET"})
     */
   /*public function show($flightNumber, FlightRepository $flightRepository): Response
    {   
        $flight = $flightRepository->findOneByFlightNumber($flightNumber);

        
        //dd($comments);
        return $this->render('flight/show.html.twig', [
            'flight' => $flight,
            'website' => 'AirAdvisor'
            
        ]);
    }*/

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Flight $flight, CallApiService $callApi): Response
    {
        $form = $this->createForm(FlightType::class, $flight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('flight_index');
        }

        return $this->render('flight/edit.html.twig', [
            'flight' => $flight,
            'form' => $form->createView(),
            'website' => 'flightAdvisor'
        ]);
    }

    /**
     * @Route("/{id}/a/{iataCode}/d/{flightDate}/flightnumber/{flightNumber}", name="show")
     */
    public function showFlight($id,$flightDate,$iataCode,$flightNumber, CallApiService $callApiService): Response
    {
    
        $flights = $callApiService->getFlightByFlightNumber($iataCode,$flightDate,$flightNumber);
        dd($flights);
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

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Flight $flight): Response
    {
        if ($this->isCsrfTokenValid('delete'.$flight->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($flight);
            $entityManager->flush();
        }

        return $this->redirectToRoute('flight_index');
    }
}
