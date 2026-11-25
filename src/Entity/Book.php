<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\BookStatus;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'books')]
#[UniqueEntity(fields: ['isbn'], message: 'Книга з таким ISBN вже зареєстрована.')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Оберіть розділ літератури.')]
    private ?Topic $topic = null;

    #[ORM\Column(length: 25, unique: true)]
    #[Assert\NotBlank(message: 'Вкажіть ISBN книги.')]
    #[Assert\Length(max: 25)]
    private ?string $isbn = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Вкажіть назву книги.')]
    #[Assert\Length(min: 2, max: 180)]
    private ?string $title = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Вкажіть автора.')]
    private ?string $author = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Range(min: 1950, max: 2026, notInRangeMessage: 'Рік видання має бути від {{ min }} до {{ max }}.')]
    private ?int $publishedYear = null;

    #[ORM\Column(length: 20, enumType: BookStatus::class)]
    private BookStatus $status = BookStatus::Available;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): static
    {
        $this->topic = $topic;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getPublishedYear(): ?int
    {
        return $this->publishedYear;
    }

    public function setPublishedYear(int $publishedYear): static
    {
        $this->publishedYear = $publishedYear;
        return $this;
    }

    public function getStatus(): BookStatus
    {
        return $this->status;
    }

    public function setStatus(BookStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }
}