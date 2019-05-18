<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByCriteria($criteria)
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($criteria as $cr) {
            switch ($cr['type']) {
                case 'eq':
                    if (!isset($cr['value'])) {
                        break;
                    }
                    $qb = $qb->andWhere("e." . $cr['property'] . " = '" . $cr['value'] . "'");
                    break;

                case 'like':
                    if (!isset($cr['value'])) {
                        break;
                    }
                    $qb = $qb->andWhere("e." . $cr['property'] . " LIKE '" . $cr['value'] . "'");
                    break;

                case 'range':
                    if (!isset($cr['value1']) || !isset($cr['value2'])) {
                        break;
                    }
                    $qb = $qb->andWhere("e." . $cr['property'] . " BETWEEN '" . $cr['value1'] . "' AND '" . $cr['value2'] . "'");
                    break;
            }
        }

        return $qb->orderBy('e.date', 'ASC')->getQuery();
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
