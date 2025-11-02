<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\EnrollmentRepository;
use App\Repository\PreRegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        // Redirection selon le rôle
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_dashboard_admin');
        }
        
        if (in_array('ROLE_TEACHER', $user->getRoles())) {
            return $this->redirectToRoute('app_dashboard_teacher');
        }
        
        if (in_array('ROLE_STUDENT', $user->getRoles())) {
            return $this->redirectToRoute('app_dashboard_student');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/admin', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(PreRegistrationRepository $preRegistrationRepository): Response
    {
        $organization = $this->getUser()->getOrganization();
        
        // Statistiques des préinscriptions
        $totalPreRegistrations = $preRegistrationRepository->countByOrganization($organization);
        $pendingPreRegistrations = $preRegistrationRepository->countByOrganizationAndStatus($organization, 'pending');
        $recentPreRegistrations = $preRegistrationRepository->findRecentByOrganization($organization, 5);
        
        return $this->render('dashboard/admin.html.twig', [
            'totalPreRegistrations' => $totalPreRegistrations,
            'pendingPreRegistrations' => $pendingPreRegistrations,
            'recentPreRegistrations' => $recentPreRegistrations,
        ]);
    }

    #[Route('/teacher', name: 'app_dashboard_teacher')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacher(
        CourseRepository $courseRepository, 
        LessonRepository $lessonRepository,
        EnrollmentRepository $enrollmentRepository
    ): Response {
        $teacher = $this->getUser();
        
        // Récupérer les cours du professeur
        $teacherCourses = $courseRepository->findBy(['teacher' => $teacher]);
        
        // Récupérer les leçons à venir (7 prochains jours)
        $upcomingLessons = $lessonRepository->findUpcomingByTeacher($teacher, 5);
        
        // Récupérer les leçons récentes (7 derniers jours)
        $recentLessons = $lessonRepository->findRecentByTeacher($teacher, 5);
        
        // Récupérer les leçons de cette semaine
        $weeklyLessons = $lessonRepository->findThisWeekByTeacher($teacher);
        
        // Compter le nombre total d'étudiants uniques
        $totalStudents = $enrollmentRepository->countUniqueStudentsByTeacher($teacher);
        
        return $this->render('dashboard/teacher.html.twig', [
            'teacherCourses' => $teacherCourses,
            'upcomingLessons' => $upcomingLessons,
            'recentLessons' => $recentLessons,
            'weeklyLessons' => $weeklyLessons,
            'totalStudents' => $totalStudents,
        ]);
    }

    #[Route('/student', name: 'app_dashboard_student')]
    #[IsGranted('ROLE_STUDENT')]
    public function student(): Response
    {
        return $this->render('dashboard/student.html.twig');
    }
}