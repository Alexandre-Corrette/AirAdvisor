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
        if (!empty($search->flightNumber)) {
            $query = $query
            ->andWhere('f.flightNumber = :flightNumber')
            ->setParameter('flightNumber', $search->flightNumber);
        }

        return $query->getQuery()->getResult();

    }

    public function findOneFlifghtByFlightNumber(string $flightNumber)  {
        $query = $this->createQueryBuilder('f');
        if(!empty($flightNumber)) {
            $query = $query->andWhere('f.flightNumber = :flightNumber')
            ->setParameter('flightNumber', $flightNumber);
        }

        return $query->getQuery()->getResult();
    }
}
