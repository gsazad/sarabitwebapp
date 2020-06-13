<?php

namespace App\Repository;

use App\Entity\YtGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method YtGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method YtGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method YtGallery[]    findAll()
 * @method YtGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YtGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YtGallery::class);
    }

    // /**
    //  * @return YtGallery[] Returns an array of YtGallery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?YtGallery
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
