<?php

namespace App\Repository;

use App\Entity\Glass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Glass>
 *
 * @method Glass|null find($id, $lockMode = null, $lockVersion = null)
 * @method Glass|null findOneBy(array $criteria, array $orderBy = null)
 * @method Glass[]    findAll()
 * @method Glass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Glass::class);
    }

//    /**
//     * @return Glass[] Returns an array of Glass objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Glass
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
