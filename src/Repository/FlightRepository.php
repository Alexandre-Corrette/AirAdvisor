<?php

namespace App\Repository;

use App\Entity\Flight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flight>
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    public function searchQueryBuilder(string $query): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.airline', 'a')
            ->leftJoin('f.reviews', 'r', 'WITH', 'r.isVisible = true')
            ->addSelect('a')
            ->addSelect('COUNT(r.id) AS reviewCount')
            ->addSelect('COALESCE(AVG(r.rating), 0) AS avgRating')
            ->groupBy('f.id, a.id');

        if ($query !== '') {
            $qb->where('f.flightNumber LIKE :q')
                ->orWhere('f.departureCity LIKE :q')
                ->orWhere('f.arrivalCity LIKE :q')
                ->orWhere('a.name LIKE :q')
                ->setParameter('q', '%' . $query . '%');
        }

        return $qb->orderBy('f.flightDate', 'DESC');
    }

    public function findByFlightNumberAndDate(string $flightNumber, \DateTimeInterface $date): ?Flight
    {
        return $this->createQueryBuilder('f')
            ->join('f.airline', 'a')
            ->leftJoin('f.reviews', 'r', 'WITH', 'r.isVisible = true')
            ->leftJoin('r.author', 'u')
            ->addSelect('a', 'r', 'u')
            ->where('f.flightNumber = :fn')
            ->andWhere('f.flightDate = :date')
            ->setParameter('fn', $flightNumber)
            ->setParameter('date', $date, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return array<array{0: Flight, reviewCount: int, avgRating: float}> */
    public function findByDestination(string $city): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.airline', 'a')
            ->leftJoin('f.reviews', 'r', 'WITH', 'r.isVisible = true')
            ->addSelect('a')
            ->addSelect('COUNT(r.id) AS reviewCount')
            ->addSelect('COALESCE(AVG(r.rating), 0) AS avgRating')
            ->where('LOWER(f.arrivalCity) LIKE LOWER(:city)')
            ->setParameter('city', '%' . $city . '%')
            ->groupBy('f.id, a.id')
            ->orderBy('f.flightDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
