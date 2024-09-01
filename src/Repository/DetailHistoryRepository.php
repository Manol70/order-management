<?php

namespace App\Repository;

use App\Entity\DetailHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailHistory>
 *
 * @method DetailHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailHistory[]    findAll()
 * @method DetailHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailHistory::class);
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
//     * @return DetailHistory[] Returns an array of DetailHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailHistory
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
