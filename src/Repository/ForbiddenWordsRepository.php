<?php

namespace App\Repository;

use App\Entity\ForbiddenWords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForbiddenWords>
 *
 * @method ForbiddenWords|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForbiddenWords|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForbiddenWords[]    findAll()
 * @method ForbiddenWords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForbiddenWordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForbiddenWords::class);
    }

//    /**
//     * @return ForbiddenWords[] Returns an array of ForbiddenWords objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ForbiddenWords
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
