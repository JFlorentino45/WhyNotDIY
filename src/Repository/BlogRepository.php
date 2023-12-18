<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 *
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findAllOrderedByLatest(): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->getQuery()
            ->getResult();
    }

    public function findMoreBlogs(int $offset): array
    {
        return $this->createQueryBuilder('b')
        ->Where('b.hidden = 0')
        ->orderBy('b.createdAt', 'DESC')
        ->setMaxResults(5)
        ->setFirstResult($offset)
        ->getQuery()
        ->getResult();
    }
    
    public function findAllOrderedByLatestAdmin(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->getQuery()
            ->getResult();
    }

    public function findMoreBlogsAdmin(int $offset): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findMyBlogsOrderedByLatest(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andWhere('b.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->getQuery()
            ->getResult();
    }

    public function findMoreMyBlogs(User $user, int $offset): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andWhere('b.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function searchBlogs($term): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andWhere('b.title LIKE :term OR b.text LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->setFirstResult(0)
            ->getQuery()
            ->getResult();
    }

    public function searchCatBlogs(string $term, int $id): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andwhere('b.title LIKE :term OR b.text LIKE :term')
            ->andWhere('b.category = :id')
            ->setParameter('id', $id)
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->setFirstResult(0)
            ->getQuery()
            ->getResult();
    }

    public function findCategoryOrderedByLatest(int $id): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andWhere('b.category = :id')
            ->setParameter('id', $id)
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(7)
            ->getQuery()
            ->getResult();
    }

    public function findMoreCategoryBlogs(int $offset, int $id): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 0')
            ->andWhere('b.category = :id')
            ->setParameter('id', $id)
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(5)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findReported(): array
    {
        return $this->createQueryBuilder('b')
            ->Where('b.hidden = 1')
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
