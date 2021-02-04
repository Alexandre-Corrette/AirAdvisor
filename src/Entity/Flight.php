<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="flight")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
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
}
