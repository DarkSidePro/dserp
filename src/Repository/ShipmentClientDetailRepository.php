<?php

namespace App\Repository;

use App\Entity\ShipmentClientDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipmentClientDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentClientDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentClientDetail[]    findAll()
 * @method ShipmentClientDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentClientDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentClientDetail::class);
    }

    // /**
    //  * @return ShipmentClientDetail[] Returns an array of ShipmentClientDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShipmentClientDetail
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
