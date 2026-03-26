<?php

namespace App\Controller;

use App\Entity\AirlineClaim;
use App\Form\AirlineClaimType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AirlineClaimController extends AbstractController
{
    #[Route('/revendiquer-compagnie', name: 'app_airline_claim', methods: ['GET', 'POST'])]
    public function claim(Request $request, EntityManagerInterface $em): Response
    {
        $claim = new AirlineClaim();
        $form = $this->createForm(AirlineClaimType::class, $claim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($claim);
            $em->flush();

            $this->addFlash('success', 'Votre demande de revendication a été envoyée. Nous la traiterons dans les plus brefs délais.');

            return $this->redirectToRoute('app_airline_claim_confirmation');
        }

        return $this->render('airline_claim/claim.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/revendiquer-compagnie/confirmation', name: 'app_airline_claim_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('airline_claim/confirmation.html.twig');
    }
}
