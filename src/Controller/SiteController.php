<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Topic;
use App\Enum\BookStatus;
use App\Repository\BookRepository;
use App\Repository\TopicRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
    public function __construct(
        private readonly TopicRepository $topics,
        private readonly BookRepository $books,
    ) {}

    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'topics' => $this->topics->withBooksCount(),
        ]);
    }

    #[Route('/topic/{slug}', name: 'site_topic', methods: ['GET'])]
    public function topic(
        #[MapEntity(mapping: ['slug' => 'slug'])] Topic $topic,
        #[MapQueryParameter] ?BookStatus $status = null,
        #[MapQueryParameter] ?string $q = null,
    ): Response {
        return $this->render('site/topic.html.twig', [
            'topic'    => $topic,
            'books'    => $this->books->findByFilters($topic, $status, $q),
            'status'   => $status,
            'statuses' => BookStatus::cases(),
            'q'        => $q,
        ]);
    }

    #[Route('/book/{id<\d+>}', name: 'site_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('site/show.html.twig', [
            'book' => $book,
        ]);
    }
}