<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\PreRegistration;
use App\Form\PreRegistrationType;
use App\Repository\OrganizationRepository;
use App\Repository\PreRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{slug}', requirements: ['slug' => '(?!login|register|admin|teacher|student|logout|_)[a-z0-9\-]+'])]
class PublicController extends AbstractController
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private PreRegistrationRepository $preRegistrationRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'public_organization_home')]
    public function home(string $slug): Response
    {
        $organization = $this->organizationRepository->findOneBy(['slug' => $slug]);
        
        if (!$organization || !$organization->isActive()) {
            throw $this->createNotFoundException('École de musique non trouvée');
        }

        return $this->render('public/home.html.twig', [
            'organization' => $organization,
        ]);
    }

    #[Route('/inscription', name: 'public_organization_registration')]
    public function registration(string $slug, Request $request): Response
    {
        $organization = $this->organizationRepository->findOneBy(['slug' => $slug]);
        
        if (!$organization || !$organization->isActive()) {
            throw $this->createNotFoundException('École de musique non trouvée');
        }

        $preRegistration = new PreRegistration();
        $preRegistration->setOrganization($organization);
        
        $form = $this->createForm(PreRegistrationType::class, $preRegistration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($preRegistration);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre demande de préinscription a été envoyée avec succès ! Nous vous contacterons très prochainement.');

            return $this->redirectToRoute('public_organization_registration_success', [
                'slug' => $slug
            ]);
        }

        return $this->render('public/registration.html.twig', [
            'organization' => $organization,
            'form' => $form,
        ]);
    }

    #[Route('/inscription/merci', name: 'public_organization_registration_success')]
    public function registrationSuccess(string $slug): Response
    {
        $organization = $this->organizationRepository->findOneBy(['slug' => $slug]);
        
        if (!$organization || !$organization->isActive()) {
            throw $this->createNotFoundException('École de musique non trouvée');
        }

        return $this->render('public/registration_success.html.twig', [
            'organization' => $organization,
        ]);
    }

    #[Route('/cours', name: 'public_organization_courses')]
    public function courses(string $slug): Response
    {
        $organization = $this->organizationRepository->findOneBy(['slug' => $slug]);
        
        if (!$organization || !$organization->isActive()) {
            throw $this->createNotFoundException('École de musique non trouvée');
        }

        // TODO: Récupérer les cours publics de l'organisation
        
        return $this->render('public/courses.html.twig', [
            'organization' => $organization,
        ]);
    }

    #[Route('/contact', name: 'public_organization_contact')]
    public function contact(string $slug): Response
    {
        $organization = $this->organizationRepository->findOneBy(['slug' => $slug]);
        
        if (!$organization || !$organization->isActive()) {
            throw $this->createNotFoundException('École de musique non trouvée');
        }

        return $this->render('public/contact.html.twig', [
            'organization' => $organization,
        ]);
    }
}