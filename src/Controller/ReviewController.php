<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\FlightRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReviewController extends AbstractController
{
    #[Route('/vol/{flightNumber}/{date}/avis', name: 'app_review_new', requirements: ['date' => '\d{4}-\d{2}-\d{2}'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        string $flightNumber,
        string $date,
        Request $request,
        FlightRepository $flightRepository,
        ReviewRepository $reviewRepository,
        EntityManagerInterface $em,
    ): Response {
        $dateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if (!$dateObj) {
            throw new NotFoundHttpException('Date invalide.');
        }

        $flight = $flightRepository->findByFlightNumberAndDate($flightNumber, $dateObj);
        if (!$flight) {
            throw $this->createNotFoundException('Vol introuvable.');
        }

        $user = $this->getUser();

        $existing = $reviewRepository->findOneBy([
            'author' => $user,
            'flight' => $flight,
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà donné votre avis sur ce vol.');

            return $this->redirectToRoute('app_flight_show', [
                'flightNumber' => $flightNumber,
                'date' => $date,
            ]);
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setAuthor($user);
            $review->setFlight($flight);

            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été publié !');

            return $this->redirectToRoute('app_flight_show', [
                'flightNumber' => $flightNumber,
                'date' => $date,
            ]);
        }

        return $this->render('review/new.html.twig', [
            'flight' => $flight,
            'form' => $form,
        ]);
    }

    #[Route('/avis/{id}/modifier', name: 'app_review_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Review $review,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        if ($review->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet avis.');
        }

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Avis modifié');

            $flight = $review->getFlight();

            return $this->redirectToRoute('app_flight_show', [
                'flightNumber' => $flight->getFlightNumber(),
                'date' => $flight->getFlightDate()->format('Y-m-d'),
            ]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'flight' => $review->getFlight(),
            'form' => $form,
        ]);
    }

    #[Route('/avis/{id}/supprimer', name: 'app_review_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(
        Review $review,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        if ($review->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cet avis.');
        }

        if (!$this->isCsrfTokenValid('delete-review-' . $review->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $flight = $review->getFlight();

        $em->remove($review);
        $em->flush();

        $this->addFlash('success', 'Avis supprimé');

        return $this->redirectToRoute('app_flight_show', [
            'flightNumber' => $flight->getFlightNumber(),
            'date' => $flight->getFlightDate()->format('Y-m-d'),
        ]);
    }
}
