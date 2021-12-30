<?php

namespace App\Controller;

use App\Entity\Flight;
use DateTimeInterface;
use App\Form\FlightType;
use App\Service\SearchJourney;
use App\Service\CallApiService;
use DateTime as GlobalDateTime;
use App\Repository\FlightRepository;
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
        
        if ($form->isSubmitted() && $form->isValid()) {
     
            
                
                
            
            

            return $this->redirectToRoute('flight_index', 
               
            );
        }

        return $this->render('flight/new.html.twig', [
            
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{flightNumber}", name="show", methods={"GET"})
     */
    public function show(Flight $flight): Response
    {   

        $comments = $flight->getComments();
        //dd($comments);
        return $this->render('flight/show.html.twig', [
            'flight' => $flight,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Flight $flight): Response
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
        ]);
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
