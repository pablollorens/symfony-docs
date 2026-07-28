<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column]
    #[Assert\Positive]
    private int $capacity;

    #[ORM\Column(type: 'json')]
    private array $equipment = [];

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\Positive]
    private string $hourlyRate;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getEquipment(): array
    {
        return $this->equipment;
    }

    public function setEquipment(array $equipment): static
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getHourlyRate(): string
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(string $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;
        return $this;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
