<?php

namespace App\Controller;

use App\Form\ProfileEditType;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(
        Request $request,
        ReviewRepository $reviewRepository,
        EntityManagerInterface $em,
    ): Response {
        $user = $this->getUser();

        $reviews = $reviewRepository->findByAuthorWithRelations($user);

        $nbReviews = count($reviews);
        $avgRating = $nbReviews > 0
            ? array_sum(array_map(fn ($r) => $r->getRating(), $reviews)) / $nbReviews
            : 0;

        $preferredAirline = null;
        if ($nbReviews > 0) {
            $airlineCounts = [];
            foreach ($reviews as $review) {
                $airline = $review->getFlight()->getAirline();
                if ($airline) {
                    $name = $airline->getName();
                    $airlineCounts[$name] = ($airlineCounts[$name] ?? 0) + 1;
                }
            }
            if ($airlineCounts) {
                arsort($airlineCounts);
                $preferredAirline = array_key_first($airlineCounts);
            }
        }

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
            'nbReviews' => $nbReviews,
            'avgRating' => $avgRating,
            'preferredAirline' => $preferredAirline,
            'form' => $form,
        ]);
    }

    #[Route('/profil/modifier', name: 'app_profile_edit', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function editProfile(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profil/{pseudo}', name: 'app_profile_public')]
    public function publicProfile(
        string $pseudo,
        UserRepository $userRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        $user = $userRepository->findOneBy(['pseudo' => $pseudo]);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $reviews = $reviewRepository->findByAuthorWithRelations($user);
        $nbReviews = count($reviews);
        $avgRating = $nbReviews > 0
            ? array_sum(array_map(fn ($r) => $r->getRating(), $reviews)) / $nbReviews
            : 0;

        return $this->render('user/profile_public.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
            'nbReviews' => $nbReviews,
            'avgRating' => $avgRating,
        ]);
    }
}
