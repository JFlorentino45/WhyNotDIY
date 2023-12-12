<?php

namespace App\Repository;

use App\Entity\ReportsC;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReportsC>
 *
 * @method ReportsC|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportsC|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportsC[]    findAll()
 * @method ReportsC[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsCRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportsC::class);
    }

//    /**
//     * @return ReportsC[] Returns an array of ReportsC objects
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

//    public function findOneBySomeField($value): ?ReportsC
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
