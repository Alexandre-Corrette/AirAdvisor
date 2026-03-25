<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FlightApiService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $aviationEdgeApiKey,
    ) {
    }

    public function searchFlight(string $flightNumber): ?array
    {
        if ($this->aviationEdgeApiKey === '') {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://aviation-edge.com/v2/public/flightsFuture', [
                'query' => [
                    'key' => $this->aviationEdgeApiKey,
                    'flightNum' => $flightNumber,
                    'type' => 'departure',
                ],
            ]);

            $data = $response->toArray(false);

            return is_array($data) && !isset($data['error']) ? $data : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function searchRoute(string $from, string $to, string $date): array
    {
        if ($this->aviationEdgeApiKey === '') {
            return [];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://aviation-edge.com/v2/public/flightsFuture', [
                'query' => [
                    'key' => $this->aviationEdgeApiKey,
                    'iataCode' => $from,
                    'type' => 'departure',
                    'arr_iataCode' => $to,
                    'date' => $date,
                ],
            ]);

            $data = $response->toArray(false);

            return is_array($data) && !isset($data['error']) ? $data : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
