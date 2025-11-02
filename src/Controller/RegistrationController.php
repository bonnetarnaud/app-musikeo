<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Admin;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Créer l'organisation
            $organization = new Organization();
            $organization->setName($data['schoolName']);
            $organization->setType('school');
            $organization->setSubscriptionPlan('free');
            $organization->setMaxStudents(30);
            $organization->setMaxTeachers(3);
            $organization->setMaxAdmins(1);
            $organization->setEmail($data['email']);
            $organization->setActive(true);
            $organization->setCreatedAt(new \DateTime());
            
            // Générer un slug unique basé sur le nom
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['schoolName'])));
            $organization->setSlug($slug . '-' . uniqid());
            
            // Créer l'utilisateur admin
            $admin = new Admin();
            $admin->setEmail($data['email']);
            $admin->setFirstName($data['firstName']);
            $admin->setLastName($data['lastName']);
            $admin->setOrganization($organization);
            
            // Hasher le mot de passe
            $admin->setPassword(
                $userPasswordHasher->hashPassword(
                    $admin,
                    $data['password']
                )
            );

            $entityManager->persist($organization);
            $entityManager->persist($admin);
            $entityManager->flush();

            // Connecter automatiquement l'utilisateur
            $security->login($admin, 'form_login');

            $this->addFlash('success', 'Votre école a été créée avec succès ! Bienvenue sur Musikeo.');

            // Rediriger vers le dashboard
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}