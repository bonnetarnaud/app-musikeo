<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/students')]
#[IsGranted('ROLE_ADMIN')]
class StudentController extends AbstractController
{
    #[Route('', name: 'app_student_index', methods: ['GET'])]
    public function index(Request $request, StudentRepository $studentRepository): Response
    {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');
        
        // Get current user's organization
        $organization = $this->getUser()->getOrganization();
        
        $students = $studentRepository->findByOrganizationWithSearch($organization, $search, $status);
        
        return $this->render('admin/student/index.html.twig', [
            'students' => $students,
            'search' => $search,
            'status' => $status,
            'organization' => $organization,
        ]);
    }

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $student = new Student();
        $student->setOrganization($this->getUser()->getOrganization());
        
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            if ($form->get('password')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($student, $form->get('password')->getData());
                $student->setPassword($hashedPassword);
            }

            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash('success', 'L\'élève a été créé avec succès.');

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/student/new.html.twig', [
            'student' => $student,
            'form' => $form,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        // Verify student belongs to current user's organization
        if ($student->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Élève introuvable.');
        }

        return $this->render('admin/student/show.html.twig', [
            'student' => $student,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Verify student belongs to current user's organization
        if ($student->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Élève introuvable.');
        }

        $form = $this->createForm(StudentType::class, $student, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password only if a new one is provided
            if ($form->get('password')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($student, $form->get('password')->getData());
                $student->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'élève a été modifié avec succès.');

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}', name: 'app_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        // Verify student belongs to current user's organization
        if ($student->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Élève introuvable.');
        }

        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            // Check if student has active enrollments
            if ($student->getEnrollments()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cet élève car il a des inscriptions actives.');
                return $this->redirectToRoute('app_student_index');
            }

            // Check if student has active instrument rentals
            if ($student->getInstrumentRentals()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cet élève car il a des locations d\'instruments en cours.');
                return $this->redirectToRoute('app_student_index');
            }

            // Check if student has payments
            if ($student->getPayments()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cet élève car il a un historique de paiements.');
                return $this->redirectToRoute('app_student_index');
            }

            $entityManager->remove($student);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'élève a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
    }
}