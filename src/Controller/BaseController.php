<?php

namespace App\Controller;

use App\Service\SearchJourneyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    /**
     * @var SearchJourneyService
     */
    private $searchJourneyService;

    public function __construct(SearchJourneyService $searchJourneyService)
    {
        $this->searchJourneyService = $searchJourneyService;
    }

    /**
     * Get the value of searchJourneyService
     *
     * @return  SearchJourneyService
     */ 
    public function getSearchJourneyService()
    {
        return $this->searchJourneyService;
    }
}