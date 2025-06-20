<?php

namespace App\Repository;

use App\Entity\Prestation;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestation>
 */
class PrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestation::class);
    }

    //    /**
    //     * @return Prestation[] Returns an array of Prestation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Prestation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByEventQueryBuilder(Event $event)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.event = :event')
            ->setParameter('event', $event)
            ->orderBy('p.nom', 'ASC');
    }

    public function getTotals(Event $event): array
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.prix) as totalPrix, SUM(p.marge) as totalMarge')
            ->andWhere('p.event = :event')
            ->setParameter('event', $event)
            ->getQuery()
            ->getSingleResult();
    }
}
