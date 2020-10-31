<?php

namespace App\Repository;

use App\Entity\PageSectionImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageSectionImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageSectionImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageSectionImages[]    findAll()
 * @method PageSectionImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageSectionImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageSectionImages::class);
    }

    // /**
    //  * @return PageSectionImages[] Returns an array of PageSectionImages objects
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
    public function findOneBySomeField($value): ?PageSectionImages
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
