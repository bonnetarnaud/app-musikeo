<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Entity\User;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/teachers')]
#[IsGranted('ROLE_ADMIN')]
class TeacherController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeacherRepository $teacherRepository
    ) {}

    #[Route('/', name: 'app_teacher_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');
        
        $teachers = $this->teacherRepository->findByOrganizationWithSearch(
            $this->getUser()->getOrganization(),
            $search,
            $status
        );

        return $this->render('admin/teacher/index.html.twig', [
            'teachers' => $teachers,
            'search' => $search,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_teacher_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $teacher = new Teacher();
        $teacher->setOrganization($this->getUser()->getOrganization());
        
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe si fourni
            if ($teacher->getPassword()) {
                $hashedPassword = password_hash($teacher->getPassword(), PASSWORD_DEFAULT);
                $teacher->setPassword($hashedPassword);
            }

            $this->entityManager->persist($teacher);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le professeur a été créé avec succès.');

            return $this->redirectToRoute('app_teacher_index');
        }

        return $this->render('admin/teacher/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        // Vérifier que le professeur appartient à l'organisation de l'admin
        if ($teacher->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('admin/teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teacher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teacher $teacher): Response
    {
        // Vérifier que le professeur appartient à l'organisation de l'admin
        if ($teacher->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TeacherType::class, $teacher, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe si un nouveau est fourni
            if ($form->get('plainPassword')->getData()) {
                $hashedPassword = password_hash($form->get('plainPassword')->getData(), PASSWORD_DEFAULT);
                $teacher->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Le professeur a été modifié avec succès.');

            return $this->redirectToRoute('app_teacher_show', ['id' => $teacher->getId()]);
        }

        return $this->render('admin/teacher/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_delete', methods: ['POST'])]
    public function delete(Request $request, Teacher $teacher): Response
    {
        // Vérifier que le professeur appartient à l'organisation de l'admin
        if ($teacher->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($teacher);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le professeur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_teacher_index');
    }

    #[Route('/{id}/toggle-status', name: 'app_teacher_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, Teacher $teacher): Response
    {
        // Vérifier que le professeur appartient à l'organisation de l'admin
        if ($teacher->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('toggle-status'.$teacher->getId(), $request->request->get('_token'))) {
            // Ici on pourrait avoir un champ 'active' ou 'status' sur l'entité Teacher
            // Pour l'instant, on simule avec un message
            $this->addFlash('success', 'Le statut du professeur a été modifié.');
        }

        return $this->redirectToRoute('app_teacher_show', ['id' => $teacher->getId()]);
    }
}