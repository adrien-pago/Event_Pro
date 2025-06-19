<?php

namespace App\Repository;

use App\Entity\DevisPrestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DevisPrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisPrestation::class);
    }

    public function findByDevis(Devis $devis): array
    {
        return $this->createQueryBuilder('dp')
            ->where('dp.devis = :devis')
            ->setParameter('devis', $devis)
            ->orderBy('dp.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPrestation(Prestation $prestation): array
    {
        return $this->createQueryBuilder('dp')
            ->where('dp.prestation = :prestation')
            ->setParameter('prestation', $prestation)
            ->orderBy('dp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
