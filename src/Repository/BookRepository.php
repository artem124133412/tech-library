<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Topic;
use App\Enum\BookStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findByFilters(?Topic $topic = null, ?BookStatus $status = null, ?string $query = null): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.topic', 't')
            ->addSelect('t')
            ->orderBy('b.title', 'ASC');

        if ($topic !== null) {
            $qb->andWhere('b.topic = :topic')
               ->setParameter('topic', $topic);
        }

        if ($status !== null) {
            $qb->andWhere('b.status = :status')
               ->setParameter('status', $status);
        }

        if ($query !== null && trim($query) !== '') {
            $qb->andWhere('b.title LIKE :q OR b.author LIKE :q OR b.isbn LIKE :q')
               ->setParameter('q', '%' . trim($query) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}