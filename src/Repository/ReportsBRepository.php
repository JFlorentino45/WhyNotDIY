<?php

namespace App\Repository;

use App\Entity\ReportsB;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReportsB>
 *
 * @method ReportsB|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportsB|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportsB[]    findAll()
 * @method ReportsB[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsBRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportsB::class);
    }

//    /**
//     * @return ReportsB[] Returns an array of ReportsB objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReportsB
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
