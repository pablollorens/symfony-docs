<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/booking')]
class BookingController extends AbstractController
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    #[Route('/new', name: 'app_booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $startAt = $booking->getStartAt();
            $endAt = $booking->getEndAt();
            $room = $booking->getRoom();

            if (!$this->bookingService->isValidDuration($startAt, $endAt)) {
                $this->addFlash('error', 'La reserva debe ser de entre 1 y 4 horas.');
                return $this->render('booking/new.html.twig', ['form' => $form]);
            }

            if ($this->bookingService->hasOverlap($room, $startAt, $endAt)) {
                $this->addFlash('error', 'La sala ya está reservada en ese horario.');
                return $this->render('booking/new.html.twig', ['form' => $form]);
            }

            $this->bookingService->createBooking($user, $room, $startAt, $endAt);
            $this->addFlash('success', 'Reserva creada correctamente.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('booking/new.html.twig', ['form' => $form]);
    }
}
