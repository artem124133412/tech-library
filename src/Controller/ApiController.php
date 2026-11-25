<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Enum\BookStatus;
use App\Repository\BookRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ApiController extends AbstractController
{
    private const array JSON_OPTIONS = [
        'json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
    ];

    public function __construct(
        private readonly TopicRepository $topics,
        private readonly BookRepository $books,
    ) {}

    #[Route('/topics', name: 'api_topics', methods: ['GET'])]
    public function topics(): JsonResponse
    {
        return $this->json($this->topics->withBooksCount(), 200, [], self::JSON_OPTIONS);
    }

    #[Route('/books', name: 'api_books', methods: ['GET'])]
    public function books(
        #[MapQueryParameter] ?BookStatus $status = null,
        #[MapQueryParameter] ?string $q = null,
    ): JsonResponse {
        $items = $this->books->findByFilters(null, $status, $q);

        return $this->json(
            array_map($this->serializeBook(...), $items),
            200,
            [],
            self::JSON_OPTIONS,
        );
    }

    #[Route('/topics/{slug}', name: 'api_topic_books', methods: ['GET'])]
    public function byTopic(string $slug): JsonResponse
    {
        $topic = $this->topics->findOneBy(['slug' => $slug]);
        if ($topic === null) {
            return $this->json(
                ['error' => "Розділ «{$slug}» не знайдено"],
                404,
                [],
                self::JSON_OPTIONS,
            );
        }

        $items = $this->books->findByFilters($topic);

        return $this->json([
            'topic' => ['slug' => $topic->getSlug(), 'name' => $topic->getName()],
            'books' => array_map($this->serializeBook(...), $items),
        ], 200, [], self::JSON_OPTIONS);
    }

    private function serializeBook(Book $b): array
    {
        return [
            'id'             => $b->getId(),
            'isbn'           => $b->getIsbn(),
            'topic'          => $b->getTopic()?->getSlug(),
            'title'          => $b->getTitle(),
            'author'         => $b->getAuthor(),
            'published_year' => $b->getPublishedYear(),
            'status'         => $b->getStatus()->label(),
            'summary'        => $b->getSummary(),
        ];
    }
}