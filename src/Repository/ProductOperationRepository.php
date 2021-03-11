<?php

namespace App\Repository;

use App\Entity\ProductOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductOperation[]    findAll()
 * @method ProductOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductOperation::class);
    }

    // /**
    //  * @return ProductOperation[] Returns an array of ProductOperation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductOperation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
