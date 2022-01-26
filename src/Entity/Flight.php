<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FlightRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Date;

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
    public $flightNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departureCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $arrivalCity;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="flight")
     */
    private $comments;

    /**
     * @ORM\Column(type="date")
     */
    protected ?DateTime $flightDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scheduledTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departureIataCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $arrivalIataCode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $arrivalScheduledTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $flightIataCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codesharedAirlineName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codesharedAirlineIataCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $airlineName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $airlineIataCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $codesharedFlightNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codesharedFlightIataNumber;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
       
    }
    public function getFlight() {
        return $this;
    }
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

    /**
     * @return array
     */
    public function getComments(): array
    {
        return $this->comments->toArray();
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFlight($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFlight() === $this) {
                $comment->setFlight(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->flightNumber;
    }

    public function getFlightDate(): ?DateTime
    {   
        
        return$this->flightDate;
    }

    public function setFlightDate(?\DateTime $flightDate): self
    {
        $this->flightDate = $flightDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getScheduledTime(): ?\DateTimeInterface
    {
        return $this->scheduledTime;
    }

    public function setScheduledTime(?\DateTimeInterface $scheduledTime): self
    {
        $this->scheduledTime = $scheduledTime;

        return $this;
    }

    public function getDepartureIataCode(): ?string
    {
        return $this->departureIataCode;
    }

    public function setDepartureIataCode(?string $departureIataCode): self
    {
        $this->departureIataCode = $departureIataCode;

        return $this;
    }

    public function getArrivalIataCode(): ?string
    {
        return $this->arrivalIataCode;
    }

    public function setArrivalIataCode(?string $arrivalIataCode): self
    {
        $this->arrivalIataCode = $arrivalIataCode;

        return $this;
    }

    public function getArrivalScheduledTime(): ?\DateTimeInterface
    {
        return $this->arrivalScheduledTime;
    }

    public function setArrivalScheduledTime(?\DateTimeInterface $arrivalScheduledTime): self
    {
        $this->arrivalScheduledTime = $arrivalScheduledTime;

        return $this;
    }

    public function getFlightIataCode(): ?string
    {
        return $this->flightIataCode;
    }

    public function setFlightIataCode(?string $flightIataCode): self
    {
        $this->flightIataCode = $flightIataCode;

        return $this;
    }

    public function getCodesharedAirlineName(): ?string
    {
        return $this->codesharedAirlineName;
    }

    public function setCodesharedAirlineName(?string $codesharedAirlineName): self
    {
        $this->codesharedAirlineName = $codesharedAirlineName;

        return $this;
    }

    public function getCodesharedAirlineIataCode(): ?string
    {
        return $this->codesharedAirlineIataCode;
    }

    public function setCodesharedAirlineIataCode(?string $codesharedAirlineIataCode): self
    {
        $this->codesharedAirlineIataCode = $codesharedAirlineIataCode;

        return $this;
    }

    public function getAirlineName(): ?string
    {
        return $this->airlineName;
    }

    public function setAirlineName(?string $airlineName): self
    {
        $this->airlineName = $airlineName;

        return $this;
    }

    public function getAirlineIataCode(): ?string
    {
        return $this->airlineIataCode;
    }

    public function setAirlineIataCode(?string $airlineIataCode): self
    {
        $this->airlineIataCode = $airlineIataCode;

        return $this;
    }

    public function getCodesharedFlightNumber(): ?int
    {
        return $this->codesharedFlightNumber;
    }

    public function setCodesharedFlightNumber(?int $codesharedFlightNumber): self
    {
        $this->codesharedFlightNumber = $codesharedFlightNumber;

        return $this;
    }

    public function getCodesharedFlightIataNumber(): ?string
    {
        return $this->codesharedFlightIataNumber;
    }

    public function setCodesharedFlightIataNumber(?string $codesharedFlightIataNumber): self
    {
        $this->codesharedFlightIataNumber = $codesharedFlightIataNumber;

        return $this;
    }
}
