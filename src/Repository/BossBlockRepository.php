<?php

namespace App\Repository;

use App\Entity\BossBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BossBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method BossBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method BossBlock[]    findAll()
 * @method BossBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BossBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BossBlock::class);
    }

    // /**
    //  * @return BossBlock[] Returns an array of BossBlock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BossBlock
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
