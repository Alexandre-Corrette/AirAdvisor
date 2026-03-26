<?php

namespace App\Controller;

use App\Entity\AirlineAccount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/espace-compagnie')]
class AirlineSpaceController extends AbstractController
{
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

        return $this->render('airline_space/dashboard.html.twig', [
            'account' => $account,
        ]);
    }
}
