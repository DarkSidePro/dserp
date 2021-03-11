<?php

namespace App\Repository;

use App\Entity\RecipeDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeDetail[]    findAll()
 * @method RecipeDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeDetail::class);
    }

    // /**
    //  * @return RecipeDetail[] Returns an array of RecipeDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecipeDetail
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
