<?php

namespace App\Repository;

use App\Entity\StatusHistory;
use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<StatusHistory>
 *
 * @method StatusHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusHistory[]    findAll()
 * @method StatusHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusHistoryRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, StatusHistory::class);
        $this->entityManager = $entityManager;
    }

//    /**
//     * @return StatusHistory[] Returns an array of StatusHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatusHistory
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

        public function findByOrderId($orderId): array
        {
            return $this->createQueryBuilder('p')
                ->andWhere('p._order = :orderId')
                ->setParameter('orderId', $orderId)
                ->getQuery()
                ->getResult();
        }
        
       /* public function findByDate1($date)
        {
            return $this->createQueryBuilder('s')
                ->andwhere('DATE(s.createdAt) = :date')
                ->setParameter('date', $date->format('Y-m-d'))
                ->getQuery()
                ->getResult();

        }
        */
        public function findByDate(\DateTimeInterface $date, $statusId)
        {
            
            $query = $this->entityManager->createQuery(
                "SELECT statuses, _status, _user, _order, customer.name AS customerName, _type.name AS typeName
                FROM App\Entity\StatusHistory statuses 
                JOIN statuses.status _status
                JOIN statuses.user _user
                JOIN statuses._order _order
                JOIN _order.customer customer
                JOIN _order.type _type
                WHERE DATE(statuses.createdAt) = :date
                AND _status.id = :statusId"
            );
            $query->setParameter('date', $date->format('Y-m-d'));
            $query->setParameter('statusId', $statusId);
            $queryResult = $query->getArrayResult();

            return $queryResult;
        }
        public function findByDateAndTypeId(\DateTimeInterface $date, int $typeId)
        {
            $query = $this->entityManager->createQuery(
                "SELECT statuses, _status, _user, _order, customer.name AS customerName, _type.name AS typeName
                FROM App\Entity\StatusHistory statuses 
                JOIN statuses.status _status
                JOIN statuses.user _user
                JOIN statuses._order _order
                JOIN _order.customer customer
                JOIN _order.type _type
                WHERE DATE(statuses.createdAt) = :date
                AND _status.name = :statusName
                AND _type.id = :typeId"
            );
            $query->setParameter('date', $date->format('Y-m-d'));
            $query->setParameter('statusName', 'Готова');
            $query->setParameter('typeId', $typeId);
            $queryResult = $query->getArrayResult();

            return $queryResult;
        }
        public function findByDateTypeIdStatusId(\DateTimeInterface $date, int $typeId, int $statusId)
        {
            $query = $this->entityManager->createQuery(
                "SELECT statuses, _status, _user, _order, customer.name AS customerName, _type.name AS typeName
                FROM App\Entity\StatusHistory statuses 
                JOIN statuses.status _status
                JOIN statuses.user _user
                JOIN statuses._order _order
                JOIN _order.customer customer
                JOIN _order.type _type
                WHERE DATE(statuses.createdAt) = :date
                AND _status.id = :statusId
                AND _type.id = :typeId"
            );
            $query->setParameter('date', $date->format('Y-m-d'));
            $query->setParameter('statusId', $statusId);
            $query->setParameter('typeId', $typeId);
            $queryResult = $query->getArrayResult();

            return $queryResult;
        }
        public function findByDateAndStatusId(\DateTimeInterface $date, int $statusId)
        {
            $query = $this->entityManager->createQuery(
                "SELECT statuses, _status, _user, _order, customer.name AS customerName, _type.name AS typeName
                FROM App\Entity\StatusHistory statuses 
                JOIN statuses.status _status
                JOIN statuses.user _user
                JOIN statuses._order _order
                JOIN _order.customer customer
                JOIN _order.type _type
                WHERE DATE(statuses.createdAt) = :date 
                AND _status.id = :statusId"
            );
            $query->setParameter('date', $date->format('Y-m-d'));
            $query->setParameter('statusId', $statusId);
            $queryResult = $query->getArrayResult();

            return $queryResult;
        }
        public function findByDateAllTypeStatus(\DateTimeInterface $date)
        {
            $query = $this->entityManager->createQuery(
                "SELECT statuses, _status, _user, _order, customer.name AS customerName, _type.name AS typeName
                FROM App\Entity\StatusHistory statuses 
                JOIN statuses.status _status
                JOIN statuses.user _user
                JOIN statuses._order _order
                JOIN _order.customer customer
                JOIN _order.type _type
                WHERE DATE(statuses.createdAt) = :date"
                
            );
            $query->setParameter('date', $date->format('Y-m-d'));
            
            $queryResult = $query->getArrayResult();

            return $queryResult;
        }

}
