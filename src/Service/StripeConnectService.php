<?php

namespace App\Service;

use App\Entity\AirlineAccount;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeConnectService
{
    public function __construct(
        private string $stripeSecretKey,
        private string $stripeWebhookSecret,
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createConnectedAccount(AirlineAccount $account): string
    {
        $stripeAccount = Account::create([
            'type' => 'standard',
            'email' => $account->getEmail(),
            'business_profile' => [
                'name' => $account->getCompanyName(),
            ],
        ]);

        return $stripeAccount->id;
    }

    public function createOnboardingLink(
        string $stripeAccountId,
        string $returnUrl,
        string $refreshUrl,
    ): string {
        $link = AccountLink::create([
            'account' => $stripeAccountId,
            'return_url' => $returnUrl,
            'refresh_url' => $refreshUrl,
            'type' => 'account_onboarding',
        ]);

        return $link->url;
    }

    public function isAccountVerified(string $stripeAccountId): bool
    {
        $account = Account::retrieve($stripeAccountId);

        return $account->charges_enabled === true;
    }

    public function constructWebhookEvent(string $payload, string $sigHeader): Event
    {
        return Webhook::constructEvent($payload, $sigHeader, $this->stripeWebhookSecret);
    }
}
