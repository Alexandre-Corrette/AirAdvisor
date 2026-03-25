<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $rating = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ratingComfort = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ratingPunctuality = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ratingStaff = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ratingFood = null;

    #[ORM\Column]
    private bool $isVisible = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Flight::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flight $flight = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getRatingComfort(): ?int
    {
        return $this->ratingComfort;
    }

    public function setRatingComfort(?int $ratingComfort): static
    {
        $this->ratingComfort = $ratingComfort;
        return $this;
    }

    public function getRatingPunctuality(): ?int
    {
        return $this->ratingPunctuality;
    }

    public function setRatingPunctuality(?int $ratingPunctuality): static
    {
        $this->ratingPunctuality = $ratingPunctuality;
        return $this;
    }

    public function getRatingStaff(): ?int
    {
        return $this->ratingStaff;
    }

    public function setRatingStaff(?int $ratingStaff): static
    {
        $this->ratingStaff = $ratingStaff;
        return $this;
    }

    public function getRatingFood(): ?int
    {
        return $this->ratingFood;
    }

    public function setRatingFood(?int $ratingFood): static
    {
        $this->ratingFood = $ratingFood;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): static
    {
        $this->flight = $flight;
        return $this;
    }
}
