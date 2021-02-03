<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlightRepository::class)
 */
class Flight
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $flightNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departureCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $arrivalCity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(?string $flightNumber): self
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getDepartureCity(): ?string
    {
        return $this->departureCity;
    }

    public function setDepartureCity(?string $departureCity): self
    {
        $this->departureCity = $departureCity;

        return $this;
    }

    public function getArrivalCity(): ?string
    {
        return $this->arrivalCity;
    }

    public function setArrivalCity(?string $arrivalCity): self
    {
        $this->arrivalCity = $arrivalCity;

        return $this;
    }
}
