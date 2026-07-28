<?php

namespace App\Tests\Integration;

use App\Entity\Room;
use App\Entity\User;
use App\Service\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BookingFlowTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private BookingService $bookingService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->bookingService = static::getContainer()->get(BookingService::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        parent::tearDown();
    }

    public function testSuccessfulBookingFlow(): void
    {
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('flow@test.com');
        $user->setName('Flow Test');
        $user->setRoles([]);
        $user->setPassword($hasher->hashPassword($user, 'test123'));
        $this->em->persist($user);

        $room = new Room();
        $room->setName('Test Room');
        $room->setCapacity(6);
        $room->setEquipment(['tv']);
        $room->setHourlyRate('20.00');
        $this->em->persist($room);
        $this->em->flush();

        $start = new \DateTimeImmutable('+1 day 10:00');
        $end = new \DateTimeImmutable('+1 day 12:00');

        $this->assertFalse($this->bookingService->hasOverlap($room, $start, $end));
        $this->assertTrue($this->bookingService->isValidDuration($start, $end));

        $booking = $this->bookingService->createBooking($user, $room, $start, $end);

        $this->assertNotNull($booking->getId());
        $this->assertSame('40.00', $booking->getTotalCost());

        $this->assertTrue($this->bookingService->hasOverlap($room, $start, $end));
    }

    public function testOverlapIsDetectedCorrectly(): void
    {
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('overlap@test.com');
        $user->setName('Overlap Test');
        $user->setRoles([]);
        $user->setPassword($hasher->hashPassword($user, 'test123'));
        $this->em->persist($user);

        $room = new Room();
        $room->setName('Overlap Room');
        $room->setCapacity(4);
        $room->setEquipment([]);
        $room->setHourlyRate('15.00');
        $this->em->persist($room);
        $this->em->flush();

        $start1 = new \DateTimeImmutable('+2 days 09:00');
        $end1 = new \DateTimeImmutable('+2 days 11:00');
        $this->bookingService->createBooking($user, $room, $start1, $end1);

        $start2 = new \DateTimeImmutable('+2 days 10:00');
        $end2 = new \DateTimeImmutable('+2 days 12:00');
        $this->assertTrue($this->bookingService->hasOverlap($room, $start2, $end2));

        $start3 = new \DateTimeImmutable('+2 days 11:00');
        $end3 = new \DateTimeImmutable('+2 days 13:00');
        $this->assertFalse($this->bookingService->hasOverlap($room, $start3, $end3));
    }
}
