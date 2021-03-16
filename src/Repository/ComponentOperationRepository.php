<?php

namespace App\Repository;

use App\Entity\ComponentOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComponentOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComponentOperation[]    findAll()
 * @method ComponentOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComponentOperation::class);
    }

    // /**
    //  * @return ComponentOperation[] Returns an array of ComponentOperation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ComponentOperation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findLastState($value): ?ComponentOperation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.component = :val')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('val', 1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
