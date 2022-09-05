<?php

namespace App\Service;

use DateTime;
use App\Service\CallApiService;
use ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchJourneyService
{
    /**
     * @var string|null
     */
    public $departureCity;

    /**
     * @var string|null
     */
    public $arrivalCity;

    /**
     * @var string|null
     */
    public $flightNumber;

    /**
     * @var string
     */
    public $flightDate;

    /**
     * @var array|null
     */
    public $flights = [];

    /**
     * @var array|null
     */
    public $error = [];

    /**
     * @var CallApiService
     */
    private $callApiService;

    public function __construct(CallApiService $callApiService)
    {
        $this->callApiService = $callApiService;
    }



    //will loop over airport list to match airport iatacode
    public function searchAirportIataCode($airportList): array
    {

        foreach ($airportList as $airportData) {
            $airportCity = $airportData['nameAirport'];

            $airportIataCode = $airportData['codeIataAirport'];
            if ($this->departureCity === $airportCity) {
                $this->flights['iataCodeDepartureCity'] = $airportIataCode;
            }
            if ($this->arrivalCity === $airportCity) {
                $this->flights['iataCodeArrivalCity'] = $airportIataCode;
            }
        }


        return $this->flights;
    }

    //will find results in API for flight query
    public function getFlights(): array
    {
        $this->flights = $this->callApiService->callApiFlights($this->flights['iataCodeDepartureCity'], $this->flights['iataCodeArrivalCity'], $this->flightDate);

        if ($this->flights['success'] != false) {
            return $this->flights;
        } else return $this->error;
    }
}
