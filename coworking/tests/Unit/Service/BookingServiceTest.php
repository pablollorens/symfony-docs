<?php

namespace App\Tests\Unit\Service;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookingServiceTest extends TestCase
{
    private BookingService $service;
    private BookingRepository&MockObject $repo;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(BookingRepository::class);
        $this->service = new BookingService($this->repo);
    }

    public function testHasOverlapReturnsTrueWhenConflictExists(): void
    {
        $room = new Room();
        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 12:00');

        $this->repo->expects($this->once())
            ->method('findOverlapping')
            ->with($room, $start, $end, null)
            ->willReturn([new Booking()]);

        $this->assertTrue($this->service->hasOverlap($room, $start, $end));
    }

    public function testHasOverlapReturnsFalseWhenNoConflict(): void
    {
        $room = new Room();
        $start = new \DateTimeImmutable('2024-06-01 14:00');
        $end = new \DateTimeImmutable('2024-06-01 15:00');

        $this->repo->expects($this->once())
            ->method('findOverlapping')
            ->willReturn([]);

        $this->assertFalse($this->service->hasOverlap($room, $start, $end));
    }

    public function testIsValidDurationAcceptsOneHour(): void
    {
        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 11:00');

        $this->assertTrue($this->service->isValidDuration($start, $end));
    }

    public function testIsValidDurationAcceptsFourHours(): void
    {
        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 14:00');

        $this->assertTrue($this->service->isValidDuration($start, $end));
    }

    public function testIsValidDurationRejectsBelowOneHour(): void
    {
        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 10:30');

        $this->assertFalse($this->service->isValidDuration($start, $end));
    }

    public function testIsValidDurationRejectsAboveFourHours(): void
    {
        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 15:00');

        $this->assertFalse($this->service->isValidDuration($start, $end));
    }

    public function testCreateBookingCalculatesTotalCost(): void
    {
        $room = new Room();
        $room->setName('Sala A');
        $room->setCapacity(8);
        $room->setEquipment([]);
        $room->setHourlyRate('25.00');

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setName('Test');
        $user->setRoles([]);
        $user->setPassword('hashed');

        $start = new \DateTimeImmutable('2024-06-01 10:00');
        $end = new \DateTimeImmutable('2024-06-01 12:00');

        $this->repo->expects($this->once())->method('save');

        $booking = $this->service->createBooking($user, $room, $start, $end);

        $this->assertSame('50.00', $booking->getTotalCost());
        $this->assertSame($room, $booking->getRoom());
        $this->assertSame($user, $booking->getUser());
    }
}
