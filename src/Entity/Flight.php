<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $flightNumber = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $flightIataCode = null;

    #[ORM\Column(length: 255)]
    private ?string $departureCity = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $departureIataCode = null;

    #[ORM\Column(length: 255)]
    private ?string $arrivalCity = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $arrivalIataCode = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $flightDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $scheduledDepartureTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $scheduledArrivalTime = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Airline::class, inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Airline $airline = null;

    /** @var Collection<int, Review> */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'flight')]
    private Collection $reviews;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'flights')]
    #[ORM\JoinTable(name: 'flight_passengers')]
    private Collection $customers;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;
        return $this;
    }

    public function getFlightIataCode(): ?string
    {
        return $this->flightIataCode;
    }

    public function setFlightIataCode(?string $flightIataCode): static
    {
        $this->flightIataCode = $flightIataCode;
        return $this;
    }

    public function getDepartureCity(): ?string
    {
        return $this->departureCity;
    }

    public function setDepartureCity(string $departureCity): static
    {
        $this->departureCity = $departureCity;
        return $this;
    }

    public function getDepartureIataCode(): ?string
    {
        return $this->departureIataCode;
    }

    public function setDepartureIataCode(?string $departureIataCode): static
    {
        $this->departureIataCode = $departureIataCode;
        return $this;
    }

    public function getArrivalCity(): ?string
    {
        return $this->arrivalCity;
    }

    public function setArrivalCity(string $arrivalCity): static
    {
        $this->arrivalCity = $arrivalCity;
        return $this;
    }

    public function getArrivalIataCode(): ?string
    {
        return $this->arrivalIataCode;
    }

    public function setArrivalIataCode(?string $arrivalIataCode): static
    {
        $this->arrivalIataCode = $arrivalIataCode;
        return $this;
    }

    public function getFlightDate(): ?\DateTimeInterface
    {
        return $this->flightDate;
    }

    public function setFlightDate(\DateTimeInterface $flightDate): static
    {
        $this->flightDate = $flightDate;
        return $this;
    }

    public function getScheduledDepartureTime(): ?\DateTimeInterface
    {
        return $this->scheduledDepartureTime;
    }

    public function setScheduledDepartureTime(?\DateTimeInterface $scheduledDepartureTime): static
    {
        $this->scheduledDepartureTime = $scheduledDepartureTime;
        return $this;
    }

    public function getScheduledArrivalTime(): ?\DateTimeInterface
    {
        return $this->scheduledArrivalTime;
    }

    public function setScheduledArrivalTime(?\DateTimeInterface $scheduledArrivalTime): static
    {
        $this->scheduledArrivalTime = $scheduledArrivalTime;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAirline(): ?Airline
    {
        return $this->airline;
    }

    public function setAirline(?Airline $airline): static
    {
        $this->airline = $airline;
        return $this;
    }

    /** @return Collection<int, Review> */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setFlight($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getFlight() === $this) {
                $review->setFlight(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, User> */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(User $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
        }
        return $this;
    }

    public function removeCustomer(User $customer): static
    {
        $this->customers->removeElement($customer);
        return $this;
    }
}
