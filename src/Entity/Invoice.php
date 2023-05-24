<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentStripeRef = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentStripeDocument = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getPaymentStripeRef(): ?string
    {
        return $this->paymentStripeRef;
    }

    public function setPaymentStripeRef(string $paymentStripeRef): self
    {
        $this->paymentStripeRef = $paymentStripeRef;

        return $this;
    }

    public function getPaymentStripeDocument(): ?string
    {
        return $this->paymentStripeDocument;
    }

    public function setPaymentStripeDocument(string $paymentStripeDocument): self
    {
        $this->paymentStripeDocument = $paymentStripeDocument;

        return $this;
    }
}
