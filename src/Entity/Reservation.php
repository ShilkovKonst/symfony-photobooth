<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $eventType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addEventType = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?bool $isPaid = false;

    #[ORM\Column]
    private ?bool $isCompleted = false;

    #[ORM\Column]
    private ?bool $isRefunded = false;

    #[ORM\Column]
    private ?bool $isCanceled = false;

    #[ORM\Column]
    private ?bool $isTermsAccepted = null;

    #[ORM\Column]
    private ?int $eventZip = null;

    #[ORM\Column(length: 255)]
    private ?string $eventCity = null;

    #[ORM\Column(length: 255)]
    private ?string $eventAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventAddressAddInfo = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Machine $machine = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    #[ORM\Column(length: 255)]
    private ?string $eventPlan = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?ReservedDates $reservedDates = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getAddEventType(): ?string
    {
        return $this->addEventType;
    }

    public function setAddEventType(?string $addEventType): self
    {
        $this->addEventType = $addEventType;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function isIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function isIsRefunded(): ?bool
    {
        return $this->isRefunded;
    }

    public function setIsRefunded(bool $isRefunded): self
    {
        $this->isRefunded = $isRefunded;

        return $this;
    }

    public function isIsCanceled(): ?bool
    {
        return $this->isCanceled;
    }

    public function setIsCanceled(bool $isCanceled): self
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    public function isIsTermsAccepted(): ?bool
    {
        return $this->isTermsAccepted;
    }

    public function setIsTermsAccepted(bool $isTermsAccepted): self
    {
        $this->isTermsAccepted = $isTermsAccepted;

        return $this;
    }

    public function getEventZip(): ?int
    {
        return $this->eventZip;
    }

    public function setEventZip(int $eventZip): self
    {
        $this->eventZip = $eventZip;

        return $this;
    }

    public function getEventCity(): ?string
    {
        return $this->eventCity;
    }

    public function setEventCity(string $eventCity): self
    {
        $this->eventCity = $eventCity;

        return $this;
    }

    public function getEventAddress(): ?string
    {
        return $this->eventAddress;
    }

    public function setEventAddress(string $eventAddress): self
    {
        $this->eventAddress = $eventAddress;

        return $this;
    }

    public function getEventAddressAddInfo(): ?string
    {
        return $this->eventAddressAddInfo;
    }

    public function setEventAddressAddInfo(?string $eventAddressAddInfo): self
    {
        $this->eventAddressAddInfo = $eventAddressAddInfo;

        return $this;
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

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        // set the owning side of the relation if necessary
        if ($invoice->getReservation() !== $this) {
            $invoice->setReservation($this);
        }

        $this->invoice = $invoice;

        return $this;
    }

    public function getEventPlan(): string
    {
        return $this->eventPlan;
    }

    public function setEventPlan(string $eventPlan): self
    {
        $this->eventPlan = $eventPlan;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setReservation($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getReservation() === $this) {
                $image->setReservation(null);
            }
        }

        return $this;
    }

    public function getReservedDates(): ?ReservedDates
    {
        return $this->reservedDates;
    }

    public function setReservedDates(ReservedDates $reservedDates): self
    {
        // set the owning side of the relation if necessary
        if ($reservedDates->getReservation() !== $this) {
            $reservedDates->setReservation($this);
        }

        $this->reservedDates = $reservedDates;

        return $this;
    }
}
