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
        $searchForm = $this->createForm(SearchJourneyType::class);
        $searchForm->handleRequest($request);
        if (($searchForm->isSubmitted() && $searchForm->isValid())) 
            {  
                
                return $this->redirectToRoute('search_results',[
                   'departureCity'=>substr($_GET['search_journey']['departureCity'], -3),
                   'arrivalCity'=>substr($_GET['search_journey']['arrivalCity'], -3),        
                ]);
            }
            
                    
        
        return $this->render('landing/index.html.twig', [
            
            'website' => 'AirAdvisor',
            'flights' => $flights,
            'searchForm' => $searchForm->createView(),
            
     
         ]);
    }

   

    
    
    
}