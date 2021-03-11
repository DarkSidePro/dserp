<?php

namespace App\Repository;

use App\Entity\ShipmentClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipmentClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentClient[]    findAll()
 * @method ShipmentClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentClient::class);
    }

    // /**
    //  * @return ShipmentClient[] Returns an array of ShipmentClient objects
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
    public function findOneBySomeField($value): ?ShipmentClient
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
