<?php

namespace App\Controller\Webhook;

use App\Repository\AirlineAccountRepository;
use App\Service\StripeConnectService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'webhook_stripe', methods: ['POST'])]
    public function __invoke(
        Request $request,
        StripeConnectService $stripeService,
        AirlineAccountRepository $accountRepository,
        EntityManagerInterface $em,
        LoggerInterface $logger,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature', '');

        try {
            $event = $stripeService->constructWebhookEvent($payload, $sigHeader);
        } catch (\Stripe\Exception\SignatureVerificationException) {
            $logger->warning('Stripe webhook signature verification failed.');

            return new Response('Invalid signature', 400);
        }

        if ($event->type === 'account.updated') {
            $stripeAccount = $event->data->object;

            if ($stripeAccount->charges_enabled === true) {
                $account = $accountRepository->findOneBy([
                    'stripeAccountId' => $stripeAccount->id,
                ]);

                if ($account !== null && !$account->isVerified()) {
                    $account->setIsVerified(true);

                    if ($account->getAirline() !== null) {
                        $account->getAirline()->setIsVerified(true);
                    }

                    $em->flush();

                    $logger->info('Airline verified via Stripe Connect.', [
                        'stripeAccountId' => $stripeAccount->id,
                        'airlineAccountId' => $account->getId(),
                    ]);
                }
            }
        }

        return new Response('ok', 200);
    }
}
