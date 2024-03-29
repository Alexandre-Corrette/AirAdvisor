<?php
 
namespace App\Service;

use DateTime; 
use DateInterval;
use App\Entity\Flight;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
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
     * @var string
     */
    public $departureDate;

    /**
     * @var string
     */
    public $airline;

    /**
     * @var array
     */
    public $flights;

    /**
     * @var string
     */

     public $flightNumber;

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
       
        return $this->callApi('flightsFuture?key='.$this->accessKey.'&type=departure&iataCode='.$iataCode.'&date='.$this->departureDate.'&flight_num='.$flightNumber.'');
    }

    public function callOneFlight(string $flightNumber) 
    {
        return $this->callApi('flights?key='.$this->accessKey.'&flightIata='.$flightNumber.'&limit=10');
    }

    public function callApitHistoricFlights(): array
    {   
       
        return $this->callApi('flightsHistory?key='.$this->accessKey.'&code='.$this->departureCity.'&type=departure&date_from='.$this->departureDate.'&arr_iataCode='.$this->arrivalCity.'&flight_number='.$this->flightNumber);
    }

    public function setFlightData(?string $departureDate, ?string $departureCity, ?string $arrivalCity, ?int $flightNumber) 
    {
        if(!empty($departureDate) || !empty($flightNumber) || !empty($departureCity) || !empty($arrivalCity))
        {
            $this->departureDate = $departureDate;
            $this->flightNumber = $flightNumber;
            $this->departureCity = $departureCity;
            $this->arrivalCity = $arrivalCity;
        }
    }

    public function getFlightDatas(): array
    {   
        $flightDatas = [
            'flightDate' => $this->departureDate,
            'flightNumber' => $this->flightNumber,
            'departureCity' => $this->departureCity,
            'arrivalCity' => $this->arrivalCity

        ];
        return $flightDatas;
    }
}