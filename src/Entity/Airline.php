<?php

namespace App\Entity;

use App\Repository\AirlineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AirlineRepository::class)]
class Airline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $iataCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoUrl = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /** @var Collection<int, Flight> */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'airline')]
    private Collection $flights;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIataCode(): ?string
    {
        return $this->iataCode;
    }

    public function setIataCode(string $iataCode): static
    {
        $this->iataCode = $iataCode;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    /** @return Collection<int, Flight> */
    public function getFlights(): Collection
    {
        return $this->flights;
    }

    public function addFlight(Flight $flight): static
    {
        if (!$this->flights->contains($flight)) {
            $this->flights->add($flight);
            $flight->setAirline($this);
        }
        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            if ($flight->getAirline() === $this) {
                $flight->setAirline(null);
            }
        }
        return $this;
    }
}
