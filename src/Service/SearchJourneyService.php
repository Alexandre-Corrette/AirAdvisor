<?php

namespace App\Service;

use DateTime;
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
     * @var DateTime
     */
    public $flightDate;
    /**
     * @var string|null
     */
    public $depatureDate;

    
}