<?php

namespace App\Repository;

use App\Entity\Jour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jour>
 */
class JourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jour::class);
    }

//    /**
//     * @return Jour[] Returns an array of Jour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findByPeriode(\DateTimeInterface $debut, \DateTimeInterface $fin): array
        {
            return $this->createQueryBuilder('j')
                ->andWhere('j.dateJour BETWEEN :debut AND :fin')
                ->setParameter('debut', $debut, Types::DATE_MUTABLE)
                ->setParameter('fin', $fin, Types::DATE_MUTABLE)
                ->orderBy('j.dateJour', 'ASC')
                ->getQuery()
                ->getResult();
        }
}
