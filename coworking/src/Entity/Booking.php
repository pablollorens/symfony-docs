<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    public const MIN_DURATION_HOURS = 1;
    public const MAX_DURATION_HOURS = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private Room $room;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $endAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalCost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setRoom(Room $room): static
    {
        $this->room = $room;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getTotalCost(): string
    {
        return $this->totalCost;
    }

    public function setTotalCost(string $totalCost): static
    {
        $this->totalCost = $totalCost;
        return $this;
    }

    public function getDurationHours(): float
    {
        return ($this->endAt->getTimestamp() - $this->startAt->getTimestamp()) / 3600;
    }

    public function isActive(): bool
    {
        return $this->endAt > new \DateTimeImmutable();
    }
}
