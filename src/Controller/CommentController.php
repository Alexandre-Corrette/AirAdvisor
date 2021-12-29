<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Flight;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\FlightType;
use App\Repository\CommentRepository;
use App\Repository\FlightRepository;
use App\Service\CallApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {   
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            
        ]);
    }

    /**
     * @Route("/new/flight/{flightNumber}", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request,string $flightNumber, CallApiService $callApiService, FlightRepository $flightRepository): Response
    {
        $comment = new Comment();
        $flight = new flight;
        $flightDatas = $flightRepository->findOneByFlightNumber($flightNumber);
    
        if(empty($flightDatas)) {
            $form = $this->createForm(FlightType::class, $flight);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                $callApiService->departureCity = substr($_POST['flight']['departureCity'], -3);
                $callApiService->arrivalCity = substr($_POST['flight']['arrivalCity'], -3);
                $callApiService->departureDate = $_POST['flight']['flightDate'];
                
                $callApiService->flightNumber = substr($flightNumber, 2);
                //dd($callApiService);
                $callApiService->callApitHistoricFlights();
                
                $flight = $callApiService->setFlight();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($flight);
                $entityManager->flush();

                
                    return $this->redirectToRoute('flight_show', [
                        'id' => $flight->getId(),
                    ]);
    
                
            
                

                
            }
            
               

            
            return $this->render('flight/new.html.twig',[
                'error' => $error = "il n'y a pas de vol avec  ce numéro, vous pouvez créer ce vol",
                'form' => $form->createView(),
                'flightNumber' => $flightNumber,
            ]); 
        
          
        } else 
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setAuthor($this->getUser());
            $comment->setFlight($flight);
            $entityManager->persist($comment);
            $entityManager->flush();
        
            return $this->redirectToRoute('comment_index');
        }
        
        return $this->render('comment/new.html.twig',[
            'comment' => $comment,
            'flightNumber' => $flightNumber,
            'form' => $form->createView(),
        ]);  
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
