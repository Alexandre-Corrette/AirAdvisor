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
 * @Route("/comment", name="comment_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/commentaires", name="index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {   
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            'website' => 'flightAdvisor',
            
        ]);
    }

    /**
     * @Route("/new/flight/{flightNumber<^[0-9]+$>}/u/{pseudo}", name="new", methods={"GET","POST"})
     */
    public function new(Request $request,string $flightNumber, CallApiService $callApiService, FlightRepository $flightRepository): Response
    {
        $comment = new Comment();
        $flight = new flight;

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
            'flight' => $callApiService->getFlightDatas(),
            'form' => $form->createView(),
            ]); 
        }
    

    /**
     * @Route("/{id<^[0-9]+$>}", name="show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id<^[0-9]+$>}/edit", name="edit", methods={"GET","POST"})
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
     * @Route("/{id<^[0-9]+$>}", name="delete", methods={"DELETE"})
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

    /**
     * @Route("/commentaires/vol/{flightId<^[0-9]+$>}", name="list_comments_about_one_flight")
     */
    public function listCommentsAboutOneFlight($flightId, CommentRepository $commentRepository)
    {   
         
        return $this->render('comment/list_comments_flight.html.twig', [
            'comments' => $commentRepository->findCommentByFlightId($flightId),
            'website' => 'flightAdvisor',
        ]);
    }
}
