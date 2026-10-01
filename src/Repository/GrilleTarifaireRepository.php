<?php

namespace App\Repository;

use App\Entity\GrilleTarifaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GrilleTarifaire>
 */
class GrilleTarifaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleTarifaire::class);
    }

    //    /**
    //     * @return GrilleTarifaire[] Returns an array of GrilleTarifaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GrilleTarifaire
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function trouverPlagesHoraires(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.deb, g.fin, g.type')
            ->andWhere('g.dateDebut <= :date')
            ->andWhere('(g.dateFin IS NULL OR g.dateFin >= :date)')
            ->setParameter('date', $date)
            ->orderBy('g.deb', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
