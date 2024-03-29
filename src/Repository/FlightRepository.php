<?php

namespace App\Repository;

use App\Entity\Flight;
use App\Service\SearchJourneyService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Flight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flight[]    findAll()
 * @method Flight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    // /**
    //  * @return Flight[] Returns an array of Flight objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Flight
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    /**
     * @return Flight[] Returns an array of Flight objects
     */
    public function search(SearchJourneyService $search): array
    {
        $query = $this
            ->createQueryBuilder('f');

        if (!empty($search->departureCity)) {
            $query = $query
            ->andWhere('f.departureCity = :departureCity')
            ->setParameter('departureCity', $search->departureCity);
        }
        if (!empty($search->arrivalCity)) {
            $query = $query
            ->andWhere('f.arrivalCity = :arrivalCity')
            ->setParameter('arrivalCity', $search->arrivalCity);
        }

        return $query->getQuery()->getResult();

    }
    
    /**
     * @return ?Flight Returns  Flight objects
     */
    public function findOneFlightByFlightNumber(string $flightNumber): ?Flight
    {
        $query = $this->createQueryBuilder('f');
        if(!empty($flightNumber)) {
            $query = $query->andWhere('f.flightNumber = :flightNumber')
            ->setParameter('flightNumber', $flightNumber);
        }
        
        return $query->getQuery()->getOneOrNullResult();
    }


     /**
     * @return ?Flight Returns  Flight objects
     */
    public function findOneFlightByFlightNumberAndDate(string $flightNumber, DateTime $flightDate): ?Flight
    {
        $query = $this->createQueryBuilder('f');
        if(!empty($flightNumber)) {
            $query = $query->andWhere('f.flightNumber = :flightNumber')
            ->setParameter('flightNumber', $flightNumber);
        }
        
        if(!empty($flightDate)) {
            $query = $query->andWhere('f.flightDate = :flightDate')
            ->setParameter('flightDate', $flightDate);
        }
        return $query->getQuery()->getOneOrNullResult();
    }
}
