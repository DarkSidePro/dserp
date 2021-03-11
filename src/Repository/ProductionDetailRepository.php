<?php

namespace App\Repository;

use App\Entity\ProductionDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductionDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductionDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductionDetail[]    findAll()
 * @method ProductionDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionDetail::class);
    }

    // /**
    //  * @return ProductionDetail[] Returns an array of ProductionDetail objects
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
    public function findOneBySomeField($value): ?ProductionDetail
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
