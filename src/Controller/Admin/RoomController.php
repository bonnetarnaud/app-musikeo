<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/rooms')]
#[IsGranted('ROLE_ADMIN')]
class RoomController extends AbstractController
{
    #[Route('', name: 'app_room_index', methods: ['GET'])]
    public function index(Request $request, RoomRepository $roomRepository): Response
    {
        $organization = $this->getUser()->getOrganization();
        
        $search = $request->query->get('search', '');
        $capacity = $request->query->get('capacity', '');
        $availability = $request->query->get('availability', '');
        
        // Recherche des salles avec filtres
        $rooms = $roomRepository->findByOrganizationWithSearch($organization, $search, $capacity, $availability);
        
        return $this->render('admin/room/index.html.twig', [
            'rooms' => $rooms,
            'search' => $search,
            'capacity' => $capacity,
            'availability' => $availability,
            'organization' => $organization,
        ]);
    }

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organization = $this->getUser()->getOrganization();
        
        $room = new Room();
        $room->setOrganization($organization);
        
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'La salle "' . $room->getName() . '" a été créée avec succès.');

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/room/new.html.twig', [
            'room' => $room,
            'form' => $form,
            'organization' => $organization,
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        // Vérifier que la salle appartient à l'organisation de l'utilisateur
        if ($room->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        return $this->render('admin/room/show.html.twig', [
            'room' => $room,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la salle appartient à l'organisation de l'utilisateur
        if ($room->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La salle "' . $room->getName() . '" a été modifiée avec succès.');

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la salle appartient à l'organisation de l'utilisateur
        if ($room->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->getPayload()->getString('_token'))) {
            // Vérifier qu'il n'y a pas de leçons programmées dans cette salle
            if ($room->getLessons()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer la salle "' . $room->getName() . '" car elle a des leçons programmées. Veuillez d\'abord les déplacer vers une autre salle.');
                return $this->redirectToRoute('app_room_index');
            }

            $roomName = $room->getName();
            $entityManager->remove($room);
            $entityManager->flush();

            $this->addFlash('success', 'La salle "' . $roomName . '" a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
    }
}