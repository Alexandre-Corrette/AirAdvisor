<?php
 
namespace App\Service;

use DateTime; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
 
class CallApiService
{   
    /**
     * @var string
     */
    public $departureCity;

    /**
     * @var string
     */
    public $arrivalCity;

    /**
     * @var DateTime
     */
    public $flightDate;
    /**
     * @var string|null
     */
    public $depatureDate;

    /**
     * @var string
     */
    public $airline;

    /**
     * @var array
     */
    public $flights;

    private $client;
 
    /**
     * @var string
     */
    public $accessKey = 'b064ee-021d46';

    public function __construct(HttpClientInterface $client)
    { 
        $this->client = $client;
        
       
    }

    public function callApi(string $var) {

        $response = $this->client->request(
            'GET',
            'https://aviation-edge.com/v2/public/'.$var
        );
        $statusCode = $response->getStatusCode();
        
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
       
        //$contentType = 'application/json'
        $content = $response->getContent();
       
    
        $response = $response->toArray();
       
        return $response;

    }

    public function callApiAirports() {
       

        return $this->callApi('airportDatabase?key='.$this->accessKey);

    }


    public function callApiFlights(string $airportIataCodeDepartureCity, string $airportIataCodeArrivalCity, string $departureDate): array
    {   
            
            
        
       return $this->callApi('flightsFuture?key='.$this->accessKey.'&type=departure&iataCode='.$airportIataCodeDepartureCity.'&arr_iataCode='.$airportIataCodeArrivalCity.'&date='.$departureDate.'');
       
        
    }

    public function getAllFlights(): ?array 
    {
        return $this->flights;
    }

    public function getFlightByFlightNumber(string $iataCode, string $flightDate,string $flightNumber): array
    {   
       
        return $this->callApi('flightsFuture?key='.$this->accessKey.'&type=departure&iataCode='.$iataCode.'&date='.$flightDate.'&flight_num='.$flightNumber.'');
    }
}