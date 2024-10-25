<?php

namespace App\Repository;

use App\Entity\TypeMontage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeMontage>
 *
 * @method TypeMontage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeMontage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeMontage[]    findAll()
 * @method TypeMontage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeMontageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeMontage::class);
    }

}
