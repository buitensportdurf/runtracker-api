<?php

namespace App\Repository;

use App\Entity\Run;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class RunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Run::class);
    }

    public function findAllByYear(?int $year = null, ?int $limit = null, ?int $offset = null): Paginator
    {
        $qb = $this->createQueryBuilder('r');
        if ($year) {
            $qb->where('r.date > :beginYear')
                ->andWhere('r.date < :endYear')
                ->setParameter('beginYear', new DateTime(sprintf('%d-01-01 00:00', $year)))
                ->setParameter('endYear', new DateTime(sprintf('%d-12-31 00:00', $year)));
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return new Paginator($qb, true);
    }
}