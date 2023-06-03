<?php

namespace App\Entity;

use App\Repository\ReservedDatesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservedDatesRepository::class)]
class ReservedDates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservedDatesEntity')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Machine $machine = null;

    #[ORM\OneToOne(inversedBy: 'reservedDates', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\Column(type: Types::JSON)]
    private ?array $dates = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMachine(): ?Machine
    {
        return $this->machine;
    }

    public function setMachine(?Machine $machine): self
    {
        $this->machine = $machine;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getDates(): array
    {
        return $this->dates;
    }

    public function setDates(array $dates): self
    {
        $this->dates = $dates;

        return $this;
    }
}
