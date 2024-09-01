<?php

namespace App\Repository;

use App\Entity\GlassHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GlassHistory>
 *
 * @method GlassHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlassHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlassHistory[]    findAll()
 * @method GlassHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlassHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlassHistory::class);
    }

    public function findByOrderId($orderId): array
        {
            return $this->createQueryBuilder('p')
                ->andWhere('p._order = :orderId')
                ->setParameter('orderId', $orderId)
                ->getQuery()
                ->getResult();
        }   

//    /**
//     * @return GlassHistory[] Returns an array of GlassHistory objects
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

//    public function findOneBySomeField($value): ?GlassHistory
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
