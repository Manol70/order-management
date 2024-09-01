<?php

namespace App\Repository;

use App\Entity\MosquitoHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MosquitoHistory>
 *
 * @method MosquitoHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method MosquitoHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method MosquitoHistory[]    findAll()
 * @method MosquitoHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MosquitoHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MosquitoHistory::class);
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
//     * @return MosquitoHistory[] Returns an array of MosquitoHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MosquitoHistory
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
