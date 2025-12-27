<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Service\OrganizationService;
use App\Service\UserService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class RegistrationController extends AbstractController
{
    public function __construct(
        private OrganizationService $organizationService,
        private UserService $userService,
        private EmailService $emailService,
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Créer l'organisation via le service
            $organization = $this->organizationService->createOrganization(
                name: $data['schoolName'],
                email: $data['email']
            );
            
            // Créer l'utilisateur admin via le service
            $admin = $this->userService->createAdmin(
                email: $data['email'],
                firstName: $data['firstName'],
                lastName: $data['lastName'],
                password: $data['password'],
                organization: $organization
            );

            $this->entityManager->flush();

            // Envoyer l'email de bienvenue de manière asynchrone
            $this->emailService->sendWelcomeEmail(
                to: $admin->getEmail(),
                firstName: $admin->getFirstName(),
                organizationName: $organization->getName()
            );

            // Connecter automatiquement l'utilisateur
            $this->security->login($admin, 'form_login');

            $this->addFlash('success', 'Votre école a été créée avec succès ! Bienvenue sur Musikeo.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}