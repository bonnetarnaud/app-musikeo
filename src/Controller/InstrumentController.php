<?php

namespace App\Controller;

use App\Entity\Instrument;
use App\Entity\Student;
use App\Form\InstrumentType;
use App\Repository\InstrumentRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/instruments')]
#[IsGranted('ROLE_ADMIN')]
class InstrumentController extends AbstractController
{
    #[Route('/', name: 'app_instrument_index', methods: ['GET'])]
    public function index(InstrumentRepository $instrumentRepository): Response
    {
        $user = $this->getUser();
        $organization = $user->getOrganization();
        
        $instruments = $instrumentRepository->findBy(['organization' => $organization]);
        
        // Statistiques
        $totalInstruments = count($instruments);
        $availableInstruments = count(array_filter($instruments, fn($i) => $i->isAvailableForRent()));
        $rentedInstruments = count(array_filter($instruments, fn($i) => $i->isCurrentlyRented()));
        $rentableInstruments = count(array_filter($instruments, fn($i) => $i->isRentable()));

        return $this->render('admin/instrument/index.html.twig', [
            'instruments' => $instruments,
            'stats' => [
                'total' => $totalInstruments,
                'available' => $availableInstruments,
                'rented' => $rentedInstruments,
                'rentable' => $rentableInstruments,
            ]
        ]);
    }

    #[Route('/new', name: 'app_instrument_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instrument = new Instrument();
        $instrument->setOrganization($this->getUser()->getOrganization());
        
        $form = $this->createForm(InstrumentType::class, $instrument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($instrument);
            $entityManager->flush();

            $this->addFlash('success', 'Instrument ajouté avec succès !');
            return $this->redirectToRoute('app_instrument_index');
        }

        return $this->render('admin/instrument/new.html.twig', [
            'instrument' => $instrument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instrument_show', methods: ['GET'])]
    public function show(Instrument $instrument): Response
    {
        $this->checkOrganization($instrument);
        
        return $this->render('admin/instrument/show.html.twig', [
            'instrument' => $instrument,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instrument_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        $this->checkOrganization($instrument);
        
        $form = $this->createForm(InstrumentType::class, $instrument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Instrument modifié avec succès !');
            return $this->redirectToRoute('app_instrument_index');
        }

        return $this->render('admin/instrument/edit.html.twig', [
            'instrument' => $instrument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instrument_delete', methods: ['POST'])]
    public function delete(Request $request, Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        $this->checkOrganization($instrument);
        
        if ($this->isCsrfTokenValid('delete'.$instrument->getId(), $request->request->get('_token'))) {
            $entityManager->remove($instrument);
            $entityManager->flush();
            
            $this->addFlash('success', 'Instrument supprimé avec succès !');
        }

        return $this->redirectToRoute('app_instrument_index');
    }

    #[Route('/{id}/rent', name: 'app_instrument_rent', methods: ['GET', 'POST'])]
    public function rent(Request $request, Instrument $instrument, StudentRepository $studentRepository, EntityManagerInterface $entityManager): Response
    {
        $this->checkOrganization($instrument);
        
        if (!$instrument->isAvailableForRent()) {
            $this->addFlash('error', 'Cet instrument n\'est pas disponible à la location.');
            return $this->redirectToRoute('app_instrument_show', ['id' => $instrument->getId()]);
        }

        $organization = $this->getUser()->getOrganization();
        $students = $studentRepository->findBy(['organization' => $organization]);

        if ($request->isMethod('POST')) {
            $studentId = $request->request->get('student_id');
            $monthlyPrice = $request->request->get('monthly_price');
            $notes = $request->request->get('notes');

            $student = $studentRepository->find($studentId);
            
            if ($student && $student->getOrganization() === $organization) {
                $rental = $instrument->rentTo($student, new \DateTime(), $monthlyPrice, $notes);
                $entityManager->persist($rental);
                $entityManager->flush();

                $this->addFlash('success', 'Instrument loué avec succès !');
                return $this->redirectToRoute('app_instrument_show', ['id' => $instrument->getId()]);
            }
        }

        return $this->render('admin/instrument/rent.html.twig', [
            'instrument' => $instrument,
            'students' => $students,
        ]);
    }

    #[Route('/{id}/return', name: 'app_instrument_return', methods: ['POST'])]
    public function returnInstrument(Request $request, Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        $this->checkOrganization($instrument);
        
        if ($this->isCsrfTokenValid('return'.$instrument->getId(), $request->request->get('_token'))) {
            if ($instrument->isCurrentlyRented()) {
                $instrument->returnFromRent();
                $entityManager->flush();
                
                $this->addFlash('success', 'Instrument retourné avec succès !');
            } else {
                $this->addFlash('error', 'Cet instrument n\'est pas actuellement loué.');
            }
        }

        return $this->redirectToRoute('app_instrument_show', ['id' => $instrument->getId()]);
    }

    private function checkOrganization(Instrument $instrument): void
    {
        if ($instrument->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }
    }
}
