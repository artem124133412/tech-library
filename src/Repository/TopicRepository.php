<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function withBooksCount(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id AS id', 't.slug AS slug', 't.name AS name', 'COUNT(b.id) AS books_count')
            ->leftJoin('t.books', 'b')
            ->groupBy('t.id')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}