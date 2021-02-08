<?php

namespace App\Controller;

use App\Entity\Flight;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class LandingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): response
    {
        $flights = $this->getDoctrine()
            ->getRepository(Flight::class)
            ->findAll();
       
        return $this->render('landing/index.html.twig', [

            'website' => 'AirAdvisor',
            'flights' => $flights,
     
         ]);
    }
    
    
}