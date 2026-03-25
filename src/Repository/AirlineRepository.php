<?php

namespace App\Repository;

use App\Entity\Airline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Airline>
 */
class AirlineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Airline::class);
    }

    /** @return array<array{airline: Airline, reviewCount: int, avgRating: float}> */
    public function findTopByReviewCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.flights', 'f')
            ->join('f.reviews', 'r')
            ->andWhere('r.isVisible = true')
            ->addSelect('COUNT(r.id) AS reviewCount')
            ->addSelect('AVG(r.rating) AS avgRating')
            ->groupBy('a.id')
            ->orderBy('reviewCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySlug(string $slug): ?Airline
    {
        return $this->createQueryBuilder('a')
            ->where('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return array<array{0: Airline, reviewCount: int, avgRating: float, flightCount: int}> */
    public function findAllWithStats(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.flights', 'f')
            ->leftJoin('f.reviews', 'r', 'WITH', 'r.isVisible = true')
            ->addSelect('COUNT(DISTINCT f.id) AS flightCount')
            ->addSelect('COUNT(r.id) AS reviewCount')
            ->addSelect('COALESCE(AVG(r.rating), 0) AS avgRating')
            ->groupBy('a.id')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<array{0: mixed, reviewCount: int, avgRating: float}> */
    public function findFlightsWithStatsByAirline(Airline $airline): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('f', 'COUNT(r.id) AS reviewCount', 'COALESCE(AVG(r.rating), 0) AS avgRating')
            ->from(\App\Entity\Flight::class, 'f')
            ->leftJoin('f.reviews', 'r', 'WITH', 'r.isVisible = true')
            ->where('f.airline = :airline')
            ->setParameter('airline', $airline)
            ->groupBy('f.id')
            ->orderBy('f.flightDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return array{avgRating: float, reviewCount: int} */
    public function getAirlineStats(Airline $airline): array
    {
        $result = $this->getEntityManager()->createQueryBuilder()
            ->select('COALESCE(AVG(r.rating), 0) AS avgRating', 'COUNT(r.id) AS reviewCount')
            ->from(\App\Entity\Review::class, 'r')
            ->join('r.flight', 'f')
            ->where('f.airline = :airline')
            ->andWhere('r.isVisible = true')
            ->setParameter('airline', $airline)
            ->getQuery()
            ->getSingleResult();

        return [
            'avgRating' => (float) $result['avgRating'],
            'reviewCount' => (int) $result['reviewCount'],
        ];
    }
}
