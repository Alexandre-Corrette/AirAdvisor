<?php

namespace App\Controller;

use App\Repository\FlightRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route("/recherche", name: "app_search")]
    public function index(Request $request, FlightRepository $flightRepository): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;

        $qb = $flightRepository->searchQueryBuilder($query);

        $countQb = clone $qb;
        $total = count($countQb->getQuery()->getResult());
        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);

        $results = $flightRepository->searchQueryBuilder($query)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'results' => $results,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }
}
