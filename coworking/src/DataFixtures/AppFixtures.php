<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@coworking.com');
        $admin->setName('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@coworking.com');
        $user->setName('John Doe');
        $user->setRoles([]);
        $user->setPassword($this->hasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        $rooms = [];
        $roomsData = [
            ['Sala A', 8, ['tv', 'projector'], '25.00'],
            ['Sala B', 12, ['tv', 'whiteboard'], '35.00'],
            ['Sala C', 4, ['whiteboard'], '15.00'],
            ['Sala Magna', 20, ['tv', 'whiteboard', 'projector'], '60.00'],
        ];

        foreach ($roomsData as [$name, $cap, $equip, $rate]) {
            $room = new Room();
            $room->setName($name);
            $room->setCapacity($cap);
            $room->setEquipment($equip);
            $room->setHourlyRate($rate);
            $manager->persist($room);
            $rooms[] = $room;
        }

        $booking = new Booking();
        $booking->setUser($user);
        $booking->setRoom($rooms[0]);
        $booking->setStartAt(new \DateTimeImmutable('+1 day 10:00'));
        $booking->setEndAt(new \DateTimeImmutable('+1 day 12:00'));
        $booking->setTotalCost('50.00');
        $manager->persist($booking);

        $pastBooking = new Booking();
        $pastBooking->setUser($user);
        $pastBooking->setRoom($rooms[1]);
        $pastBooking->setStartAt(new \DateTimeImmutable('-5 days 14:00'));
        $pastBooking->setEndAt(new \DateTimeImmutable('-5 days 16:00'));
        $pastBooking->setTotalCost('70.00');
        $manager->persist($pastBooking);

        $manager->flush();
    }
}
