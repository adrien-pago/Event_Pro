<?php

namespace App\Repository;

use App\Entity\Prestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestation::class);
    }

    public function findActivePrestations(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = true')
            ->orderBy('p.category', 'ASC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPrestationsByCategory(string $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.isActive = true')
            ->setParameter('category', $category)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
