<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Status>
 *
 * @method Status|null find($id, $lockMode = null, $lockVersion = null)
 * @method Status|null findOneBy(array $criteria, array $orderBy = null)
 * @method Status[]    findAll()
 * @method Status[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    public function determineNewStatus($currentStatus)
    {
        return $this->createQueryBuilder('s')
            ->select('MIN(s.id)')
            ->where('s.id > :currentStatusId')
            ->setParameter('currentStatusId', $currentStatus)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function lastStatusOrder(){
        return $this->createQueryBuilder('s')
        ->select('MAX(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        
    }

}
