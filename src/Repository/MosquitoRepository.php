<?php

namespace App\Repository;

use App\Entity\Mosquito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mosquito>
 *
 * @method Mosquito|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mosquito|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mosquito[]    findAll()
 * @method Mosquito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MosquitoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mosquito::class);
    }

//    /**
//     * @return Mosquito[] Returns an array of Mosquito objects
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

//    public function findOneBySomeField($value): ?Mosquito
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
