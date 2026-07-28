<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingRepository;

class BookingService
{
    public function __construct(private readonly BookingRepository $bookingRepository)
    {
    }

    public function hasOverlap(
        Room $room,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        ?int $excludeId = null
    ): bool {
        return count($this->bookingRepository->findOverlapping($room, $startAt, $endAt, $excludeId)) > 0;
    }

    public function isValidDuration(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): bool
    {
        $hours = ($endAt->getTimestamp() - $startAt->getTimestamp()) / 3600;

        return $hours >= Booking::MIN_DURATION_HOURS && $hours <= Booking::MAX_DURATION_HOURS;
    }

    public function createBooking(User $user, Room $room, \DateTimeImmutable $startAt, \DateTimeImmutable $endAt): Booking
    {
        $hours = ($endAt->getTimestamp() - $startAt->getTimestamp()) / 3600;
        $totalCost = bcmul((string) $hours, $room->getHourlyRate(), 2);

        $booking = new Booking();
        $booking->setUser($user);
        $booking->setRoom($room);
        $booking->setStartAt($startAt);
        $booking->setEndAt($endAt);
        $booking->setTotalCost($totalCost);

        $this->bookingRepository->save($booking, true);

        return $booking;
    }
}
