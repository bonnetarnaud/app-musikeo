<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
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
    public function admin(): Response
    {
        return $this->render('dashboard/admin.html.twig');
    }

    #[Route('/teacher', name: 'app_dashboard_teacher')]
    #[IsGranted('ROLE_TEACHER')]
    public function teacher(): Response
    {
        return $this->render('dashboard/teacher.html.twig');
    }

    #[Route('/student', name: 'app_dashboard_student')]
    #[IsGranted('ROLE_STUDENT')]
    public function student(): Response
    {
        return $this->render('dashboard/student.html.twig');
    }
}