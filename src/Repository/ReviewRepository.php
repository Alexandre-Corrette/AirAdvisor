<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /** @return Review[] */
    public function findByAuthorWithRelations(object $user): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.flight', 'f')
            ->join('f.airline', 'al')
            ->addSelect('f', 'al')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Review[] */
    public function findLatestWithRelations(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.author', 'a')
            ->join('r.flight', 'f')
            ->join('f.airline', 'al')
            ->addSelect('a', 'f', 'al')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
