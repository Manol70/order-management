<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Order::class);
    }

    public function getOrdersFullyLoad() {
        $query = $this->entityManager->createQuery("SELECT orders, customer, type_montage, types FROM App\Entity\Order orders LEFT JOIN orders.customer customer 
                                                    LEFT JOIN orders.type_montage type_montage LEFT JOIN orders.type types");
        $queryResult = $query->getArrayResult();
        dd($queryResult);
        

        return $queryResult;
    }

    public function getUnpaidOrdersByCustomerId($customerId){
        $query = $this->entityManager->createQuery("SELECT orders, customer FROM App\Entity\Order orders LEFT JOIN orders.customer customer where orders.customer = $customerId and (orders.price - orders.paid) > 0 ");
        $queryResult = $query->getArrayResult();
        //dd($queryResult);
        return $queryResult;
    }

    public function getOrderById($orderId){
        $query = $this->entityManager->createQuery("SELECT orders FROM App\Entity\Order orders where orders.id = $orderId");
        $queryResult = $query->getOneOrNullResult();
        //dd($queryResult);
        return $queryResult;
    }

    public function getOrdersByCustomerId(int $customerId)
    {
        $query = $this->entityManager->createQuery(
            "SELECT orders, customer, _type 
            FROM App\Entity\Order orders 
            JOIN orders.customer customer
            JOIN orders.type _type
            WHERE customer.id = :customerId"
            );
            $query->setParameter('customerId', $customerId);
            $queryResult = $query->getArrayResult();
    
        return $queryResult;
    }
 
    public function findByCustomerId(int $customerId)
    {
        $query = $this->entityManager->createQuery(
            "SELECT orders.id, orders.number, orders.createdAt, orders.for_date, orders.scheme,
            orders.price, orders.paid, orders.quadrature, _type.name AS typeName, _status.name AS statusName,
            glass.name AS glassName, mosquito.name AS mosquitoName, detail.name AS detailName,
            customer.name AS customerName
            FROM App\Entity\Order orders 
            JOIN orders.customer customer
            JOIN orders.type _type
            JOIN orders.status _status
            JOIN orders.glass glass
            JOIN orders.mosquito mosquito
            JOIN orders.detail detail
            WHERE customer.id = :customerId"
        );
        $query->setParameter('customerId', $customerId);
        $queryResult = $query->getArrayResult();

        return $queryResult;
    }
    


    public function createQueryBuilderForAllOrders()
{
    return $this->createQueryBuilder('o')
                ->orderBy('o.id', 'DESC');
}

public function getTotalOrders( $fromDate,  $toDate)
{ 
    return $this->createQueryBuilder('o')
        ->select('COUNT(o.id) as total_orders')
        ->where('o.createdAt BETWEEN :createdAt AND :to_date')
        ->setParameter('createdAt', $fromDate)
        ->setParameter('to_date', $toDate)
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTotalQuadrature( $fromDate,  $toDate)
{ 
    return $this->createQueryBuilder('o')
        ->select('SUM(o.quadrature) as total_quadrature')
        ->where('o.createdAt BETWEEN :createdAt AND :to_date')
        ->setParameter('createdAt', $fromDate)
        ->setParameter('to_date', $toDate)
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTotalAmount( $fromDate,  $toDate)
{ 
    return $this->createQueryBuilder('o')
        ->select('SUM(o.price) as total_amount')
        ->where('o.createdAt BETWEEN :createdAt AND :to_date')
        ->setParameter('createdAt', $fromDate)
        ->setParameter('to_date', $toDate)
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTotalPaid( $fromDate,  $toDate)
{ 
    return $this->createQueryBuilder('o')
        ->select('SUM(o.paid) as total_paid')
        ->where('o.createdAt BETWEEN :createdAt AND :to_date')
        ->setParameter('createdAt', $fromDate)
        ->setParameter('to_date', $toDate)
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTopTurnover(EntityManager $entityManager, $fromDate,  $toDate)
{ 
    $query = $entityManager->createQuery(
        'SELECT 
            c.id AS customerId,
            c.name AS customerName,
            SUM(o.price) AS totalRevenue
        FROM App\Entity\Order o
        JOIN o.customer c
        WHERE o.createdAt BETWEEN :from_date AND :to_date
        GROUP BY c.id
        ORDER BY totalRevenue DESC'
    )
    ->setParameter('from_date', $fromDate)
    ->setParameter('to_date', $toDate)
    ->setMaxResults(10);
    $result = $query->getResult();
    return $result;
}

public function getTopQuadratureByCustomer(EntityManager $entityManager, $fromDate,  $toDate)
{
    $query = $entityManager->createQuery(
        'SELECT 
            c.id AS customerId,
            c.name AS customerName,
            SUM(o.quadrature) AS totalQuadrature
        FROM App\Entity\Order o
        JOIN o.customer c
        WHERE o.createdAt BETWEEN :from_date AND :to_date
        GROUP BY c.id
        ORDER BY totalQuadrature DESC'
    )
    ->setParameter('from_date', $fromDate)
    ->setParameter('to_date', $toDate)
    ->setMaxResults(10);
    $result = $query->getResult();
    return $result;
}

public function getTopCountOrderByCustomer(EntityManager $entityManager, $fromDate,  $toDate)
{
    $query = $entityManager->createQuery(
        'SELECT 
            c.id AS customerId,
            c.name AS customerName,
            COUNT(o.id) AS orderCount
        FROM App\Entity\Order o
        JOIN o.customer c
        WHERE o.createdAt BETWEEN :from_date AND :to_date
        GROUP BY c.id
        ORDER BY orderCount DESC'
    )
    ->setParameter('from_date', $fromDate)
    ->setParameter('to_date', $toDate)
    ->setMaxResults(10);
    $result = $query->getResult();
    return $result;   
}
   /* public function findAllOrders():array
    {
        return $this->createQueryBuilder('mix')
            ->select('mix.number')
            ->andWhere('mix.type_montage_id = 1')
            ->orderBy('mix.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
*/
//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
