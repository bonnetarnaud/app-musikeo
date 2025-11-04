<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/courses')]
#[IsGranted('ROLE_ADMIN')]
class CourseController extends AbstractController
{
    #[Route('', name: 'app_course_index', methods: ['GET'])]
    public function index(Request $request, CourseRepository $courseRepository): Response
    {
        $search = $request->query->get('search', '');
        $teacher = $request->query->get('teacher', '');
        
        // Get current user's organization
        $organization = $this->getUser()->getOrganization();
        
        $courses = $courseRepository->findByOrganizationWithSearch($organization, $search, $teacher);
        
        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses,
            'search' => $search,
            'teacher' => $teacher,
            'organization' => $organization,
        ]);
    }

    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $course->setOrganization($this->getUser()->getOrganization());
        
        $form = $this->createForm(CourseType::class, $course, [
            'organization' => $this->getUser()->getOrganization()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été créé avec succès.');

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/new.html.twig', [
            'course' => $course,
            'form' => $form,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        // Verify course belongs to current user's organization
        if ($course->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        return $this->render('admin/course/show.html.twig', [
            'course' => $course,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Verify course belongs to current user's organization
        if ($course->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        $form = $this->createForm(CourseType::class, $course, [
            'organization' => $this->getUser()->getOrganization()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été modifié avec succès.');

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
            'organization' => $this->getUser()->getOrganization(),
        ]);
    }

    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Verify course belongs to current user's organization
        if ($course->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            // Check if course has enrollments
            if ($course->getEnrollments()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce cours car des élèves y sont inscrits.');
                return $this->redirectToRoute('app_course_index');
            }

            // Check if course has lessons
            if ($course->getLessons()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer ce cours car des leçons sont programmées.');
                return $this->redirectToRoute('app_course_index');
            }

            $entityManager->remove($course);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le cours a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}