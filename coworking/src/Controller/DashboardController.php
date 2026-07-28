<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(private readonly BookingRepository $bookingRepository)
    {
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'active_bookings' => $this->bookingRepository->findActiveByUser($user),
            'past_bookings' => $this->bookingRepository->findPastByUser($user),
            'monthly_total' => $this->bookingRepository->findMonthlyTotalByUser($user),
        ]);
    }
}
