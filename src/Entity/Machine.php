<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'machine', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isAvailable = true;

    #[ORM\OneToMany(mappedBy: 'machine', targetEntity: ReservedDates::class)]
    private Collection $reservedDates;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->reservedDates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setMachine($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getMachine() === $this) {
                $reservation->setMachine(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * @return Collection<int, ReservedDates>
     */
    public function getReservedDates(): Collection
    {
        return $this->reservedDates;
    }

    public function addReservedDatesEntity(ReservedDates $reservedDatesEntity): self
    {
        if (!$this->reservedDates->contains($reservedDatesEntity)) {
            $this->reservedDates->add($reservedDatesEntity);
            $reservedDatesEntity->setMachine($this);
        }

        return $this;
    }

    public function removeReservedDatesEntity(ReservedDates $reservedDatesEntity): self
    {
        if ($this->reservedDates->removeElement($reservedDatesEntity)) {
            // set the owning side to null (unless already changed)
            if ($reservedDatesEntity->getMachine() === $this) {
                $reservedDatesEntity->setMachine(null);
            }
        }

        return $this;
    }
}
