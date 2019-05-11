<?php

namespace App\Repository;

use App\Entity\SecurityCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SecurityCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecurityCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecurityCode[]    findAll()
 * @method SecurityCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecurityCodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SecurityCode::class);
    }

    // /**
    //  * @return SecurityCode[] Returns an array of SecurityCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SecurityCode
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
