<?php

namespace App\Controller;

use ReflectionClass;
use App\Entity\Flight;
use App\Form\SearchJourneyType;
use App\Service\CallApiService;
use App\Service\SearchJourneyService;
use App\Controller\SearchJourneyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class LandingController extends AbstractController
{

  
    /**
     * @Route("/", name="index")
     */
    public function index(request $request): response
    {
        $flights = $this->getDoctrine()
            ->getRepository(Flight::class)
            ->findAll();
        foreach($flights as $flight)
        {   
            if(!empty($flight->getComments()))
            {
                $numberOfComments[] = [
                    'commentsNumber' => count($flight->getComments()),
                    'flightNumber' => $flight->getflightNumber(),
                    'comments' => $flight->getComments(),
                    'departure' => $flight->getDepartureCity(),
                    'arrival' => $flight->getArrivalCity()
                                ];
            }
           
        }
        rsort($numberOfComments);
        $searchForm = $this->createForm(SearchJourneyType::class);
        $searchForm->handleRequest($request);
        if (($searchForm->isSubmitted() && $searchForm->isValid())) 
            {  
                
                return $this->redirectToRoute('search_results',[
                   'departureCity'=>$_GET['search_journey']['departureCity'],
                   'arrivalCity'=>$_GET['search_journey']['arrivalCity'],        
                ]);
            }
            
                    
        
        return $this->render('landing/index.html.twig', [
            
            'website' => 'FlightAdvisor',
            'flights' => $flights,
            'commentsNumber' => $numberOfComments,
            'searchForm' => $searchForm->createView(),
            
     
         ]);
    }

   

    
    
    
}