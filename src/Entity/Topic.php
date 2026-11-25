<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\Table(name: 'topics')]
#[UniqueEntity(fields: ['slug'], message: 'Розділ із таким кодом уже існує.')]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40, unique: true)]
    #[Assert\NotBlank(message: 'Вкажіть URL-код розділу.')]
    #[Assert\Regex(pattern: '/^[a-z0-9-]+$/', message: 'Дозволені лише малі латинські літери, цифри та дефіс.')]
    private ?string $slug = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Назва розділу не може бути порожньою.')]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'topic')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}