<?php

namespace App\Controller;

use ReflectionClass;
use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Service\CallApiService;
use App\Controller\BaseController;
use App\Service\SearchJourneyService;
use App\Controller\SearchJourneyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class LandingController extends BaseController
{


  
    /**
     * @Route("/", name="index")
     */
    public function index(request $request): response
    {
        $flightRepository = $this->getDoctrine()
        ->getRepository(Flight::class);
        $flights = $flightRepository->findAll();
        $searchForm = $this->createForm(SearchJourneyType::class);
        $searchForm->handleRequest($request);
        if (($searchForm->isSubmitted() && $searchForm->isValid())) 
            {  
                if (!$flightRepository->findBy([
                    'departure_city' => $searchForm['departureCity'],
                    'arrival_city' => $searchForm['arrivalCity']
                ])) {
                    $this->getSearchJourneyService()->getFlights();
                } else {
                    $results = $flightRepository->findBy([
                        'departure_city' => $searchForm['departureCity'],
                        'arrival_city' => $searchForm['arrivalCity']
                    ]);
                }
                
            }
            
                    
        
        return $this->render('landing/index.html.twig', [
            
            'website' => 'FlightAdvisor',
            'flights' => $flights,
            'searchForm' => $searchForm->createView(),
            'results' => $results ?:null,
            
     
         ]);
    }

   

    
    
    
}