<?php

namespace App\Controller;

use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Repository\FlightRepository;
use App\Form\SearchCompanyFlightType;
use App\Service\SearchJourneyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("search", name="search_")
 */

class SearchJourneyController extends AbstractController
{
    /**
     * @Route("/", name="journeys")
     */
    public function search(Request $request, FlightRepository $flightRepository): Response
    {
        $search = new SearchJourneyService();
        $searchJourneyForm = $this->createForm(SearchJourneyType::class, $search);
        $searchJourneyForm->handleRequest($request);
         
            if ($searchJourneyForm->isSubmitted() && $searchJourneyForm->isValid()) {
                $flights = $flightRepository->search($search);        
            } else {
                $flights = null;
            }
        $search = new SearchJourneyService();
        $searchCompanyFlightForm = $this->createForm(SearchCompanyFlightType::class, $search);
        $searchCompanyFlightForm->handleRequest($request);

            if ($searchCompanyFlightForm->isSubmitted() && $searchCompanyFlightForm->isValid()) {
                $flights = $flightRepository->search($search);
            } else {
                $flights = null;
            }
        
        return $this->render('search/index.html.twig', [
            'searchCompanyFlightForm' => $searchCompanyFlightForm->createView(),
            'searchForm' => $searchJourneyForm->createView(),
            'website' => 'AirAdvisor',
            'flights' => $flights,
            
        ]);
        
        
    }
}