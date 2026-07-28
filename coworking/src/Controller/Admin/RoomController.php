<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/rooms')]
class RoomController extends AbstractController
{
    public function __construct(private readonly RoomRepository $roomRepository)
    {
    }

    #[Route('', name: 'admin_room_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/room/index.html.twig', [
            'rooms' => $this->roomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomRepository->save($room, true);
            $this->addFlash('success', 'Sala creada.');
            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/new.html.twig', ['form' => $form, 'room' => $room]);
    }

    #[Route('/{id}/edit', name: 'admin_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomRepository->save($room, true);
            $this->addFlash('success', 'Sala actualizada.');
            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/edit.html.twig', ['form' => $form, 'room' => $room]);
    }

    #[Route('/{id}/delete', name: 'admin_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->getPayload()->getString('_token'))) {
            $this->roomRepository->remove($room, true);
            $this->addFlash('success', 'Sala eliminada.');
        }

        return $this->redirectToRoute('admin_room_index');
    }
}
