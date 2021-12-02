<?php

namespace App\Controller;

use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Repository\FlightRepository;
use App\Form\SearchCompanyFlightType;
use App\Service\CallApiService;
use App\Service\SearchJourneyService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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
    public function search(Request $request, CallApiService $callApiService): Response
    {   
        
        $searchJourneyForm = $this->createForm(SearchJourneyType::class);
        $searchJourneyForm->handleRequest($request);
            if (($searchJourneyForm->isSubmitted() && $searchJourneyForm->isValid())) 
            {   
                if(!empty($_GET)) {
                    $query = $_GET['search_journey'];
                    
                    $datas = $callApiService->callApiHistoricalFlights($query);
                    
                }  else {
                    $datas = null;
                   
            }  
        }  else {
            $datas = null;
        }
      
        return $this->render('search/index.html.twig', [
            'searchForm' => $searchJourneyForm->createView(),
            'website' => 'AirAdvisor',
            'flights' =>  $datas['flights'],
            'departureCity' => $datas['departureCity'],
            'arrivalCity' => $datas['arrivalCity'],
            'departureDate' => $datas['departureDate'],


        ]);
    }

}