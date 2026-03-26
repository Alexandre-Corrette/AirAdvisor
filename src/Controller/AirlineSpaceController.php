<?php

namespace App\Controller;

use App\Entity\AirlineAccount;
use App\Service\StripeConnectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/espace-compagnie')]
class AirlineSpaceController extends AbstractController
{
    public function __construct(
        private StripeConnectService $stripeService,
    ) {
    }

    #[Route('/login', name: 'airline_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser() instanceof AirlineAccount) {
            return $this->redirectToRoute('airline_dashboard');
        }

        return $this->render('airline_space/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'airline_logout')]
    public function logout(): never
    {
        throw new \LogicException('Intercepted by the firewall.');
    }

    #[Route('', name: 'airline_dashboard')]
    public function dashboard(): Response
    {
        /** @var AirlineAccount $account */
        $account = $this->getUser();

        $onboardingUrl = null;
        if ($account->getStripeAccountId() && !$account->isVerified()) {
            $onboardingUrl = $this->stripeService->createOnboardingLink(
                $account->getStripeAccountId(),
                $this->generateUrl('airline_stripe_return', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->generateUrl('airline_stripe_refresh', [], UrlGeneratorInterface::ABSOLUTE_URL),
            );
        }

        return $this->render('airline_space/dashboard.html.twig', [
            'account' => $account,
            'onboardingUrl' => $onboardingUrl,
        ]);
    }

    #[Route('/stripe/return', name: 'airline_stripe_return')]
    public function stripeReturn(EntityManagerInterface $em): Response
    {
        /** @var AirlineAccount $account */
        $account = $this->getUser();

        if ($account->getStripeAccountId() && !$account->isVerified()) {
            if ($this->stripeService->isAccountVerified($account->getStripeAccountId())) {
                $account->setIsVerified(true);
                if ($account->getAirline() !== null) {
                    $account->getAirline()->setIsVerified(true);
                }
                $em->flush();
                $this->addFlash('success', 'Votre compagnie a ete verifiee avec succes.');
            } else {
                $this->addFlash('info', 'La verification est en cours. Vous serez notifie lorsqu\'elle sera terminee.');
            }
        }

        return $this->redirectToRoute('airline_dashboard');
    }

    #[Route('/stripe/refresh', name: 'airline_stripe_refresh')]
    public function stripeRefresh(): Response
    {
        /** @var AirlineAccount $account */
        $account = $this->getUser();

        if ($account->getStripeAccountId() && !$account->isVerified()) {
            $onboardingUrl = $this->stripeService->createOnboardingLink(
                $account->getStripeAccountId(),
                $this->generateUrl('airline_stripe_return', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->generateUrl('airline_stripe_refresh', [], UrlGeneratorInterface::ABSOLUTE_URL),
            );

            return $this->redirect($onboardingUrl);
        }

        return $this->redirectToRoute('airline_dashboard');
    }
}
