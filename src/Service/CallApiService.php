<?php
 
namespace App\Service;

use DateTime; 
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

    private $client;
 
    /**
     * @var string
     */
    public $accessKey = 'b064ee-021d46';

    public function __construct(HttpClientInterface $client)
    { 
        $this->client = $client;
    }

    public function callApiAirports() {
        $response = $this->client->request(
            'GET',
            'https://aviation-edge.com/v2/public/airportDatabase?key='.$this->accessKey.''
        );
        $statusCode = $response->getStatusCode();
        
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        //$contentType = 'application/json'
        $content = $response->getContent();
        
    
        $airports = $response->toArray();

        return $airports;

    }


    public function callApiHistoricalFlights(array $query): array
    {   
        $flights = [];
            
            $dataContent['arrivalCity'] = $query['arrivalCity'];
            $dataContent['departureCity'] = $query['departureCity'];
            $dataContent['departureDate'] = date('Y-m-d',strtotime($query['flightDate']));
            $airports = $this->callApiAirports();
            foreach ($airports as $airport) {
                if($dataContent['departureCity'] === $airport['nameAirport']) {
                $airportIataCodeDepartureCity = $airport['codeIataAirport'];
                }
                if($dataContent['arrivalCity'] === $airport['nameAirport']) {
                    $airportIataCodeArrivalCity = $airport['codeIataAirport'];
                    }
            }
            var_dump($airportIataCodeDepartureCity);
        
       $response = $this->client->request(
            'GET',
            'http://aviation-edge.com/v2/public/flightsFuture?key='.$this->accessKey.'&type=departure&iataCode='.$airportIataCodeDepartureCity.'&arr_iataCode='.$airportIataCodeArrivalCity.'&date='.$dataContent['departureDate'].''
        );
        $statusCode = $response->getStatusCode();
        
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        //$contentType = 'application/json'
        $content = $response->getContent();
        
    
        $dataContent['flights'] = $response->toArray();
        
         
       

        return $dataContent;
    }
}