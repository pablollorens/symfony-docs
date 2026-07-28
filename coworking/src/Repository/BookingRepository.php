<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $booking, bool $flush = false): void
    {
        $this->getEntityManager()->persist($booking);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOverlapping(
        Room $room,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        ?int $excludeId = null
    ): array {
        $qb = $this->createQueryBuilder('b')
            ->where('b.room = :room')
            ->andWhere('b.startAt < :endAt')
            ->andWhere('b.endAt > :startAt')
            ->setParameter('room', $room)
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt);

        if ($excludeId !== null) {
            $qb->andWhere('b.id != :excludeId')->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.room', 'r')
            ->where('b.user = :user')
            ->andWhere('b.endAt > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('b.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findPastByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.room', 'r')
            ->where('b.user = :user')
            ->andWhere('b.endAt <= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('b.startAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findMonthlyTotalByUser(User $user): string
    {
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('last day of this month 23:59:59');

        $result = $this->createQueryBuilder('b')
            ->select('SUM(b.totalCost) as total')
            ->where('b.user = :user')
            ->andWhere('b.startAt >= :start')
            ->andWhere('b.startAt <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }
}
