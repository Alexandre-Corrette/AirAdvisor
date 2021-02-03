<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class LandingController extends AbstractController
{
    /**
     * @Route("/landing", name="index")
     */
    public function index(): response
    {
        return $this->render('landing/index.html.twig', [

            'website' => 'AirAdvisor',
     
         ]);
    }
    
}