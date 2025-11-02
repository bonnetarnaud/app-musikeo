<?php

namespace App\Controller;

use App\Entity\PreRegistration;
use App\Repository\PreRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/preregistrations')]
#[IsGranted('ROLE_ADMIN')]
class PreRegistrationController extends AbstractController
{
    public function __construct(
        private PreRegistrationRepository $preRegistrationRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_preregistration_index')]
    public function index(Request $request): Response
    {
        $organization = $this->getUser()->getOrganization();
        $status = $request->query->get('status');
        
        if ($status) {
            $preRegistrations = $this->preRegistrationRepository->findByOrganizationAndStatus($organization, $status);
        } else {
            $preRegistrations = $this->preRegistrationRepository->findByOrganization($organization);
        }

        // Statistics
        $stats = [
            'total' => $this->preRegistrationRepository->countByOrganization($organization),
            'pending' => $this->preRegistrationRepository->countByOrganizationAndStatus($organization, PreRegistration::STATUS_PENDING),
            'contacted' => $this->preRegistrationRepository->countByOrganizationAndStatus($organization, PreRegistration::STATUS_CONTACTED),
            'enrolled' => $this->preRegistrationRepository->countByOrganizationAndStatus($organization, PreRegistration::STATUS_ENROLLED),
            'rejected' => $this->preRegistrationRepository->countByOrganizationAndStatus($organization, PreRegistration::STATUS_REJECTED),
        ];

        return $this->render('admin/preregistration/index.html.twig', [
            'preRegistrations' => $preRegistrations,
            'stats' => $stats,
            'currentStatus' => $status,
            'availableStatuses' => PreRegistration::getAvailableStatuses(),
        ]);
    }

    #[Route('/{id}', name: 'app_preregistration_show')]
    public function show(PreRegistration $preRegistration): Response
    {
        // Vérifier que la préinscription appartient à l'organisation de l'admin
        if ($preRegistration->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('admin/preregistration/show.html.twig', [
            'preRegistration' => $preRegistration,
        ]);
    }

    #[Route('/{id}/status', name: 'app_preregistration_update_status', methods: ['POST'])]
    public function updateStatus(PreRegistration $preRegistration, Request $request): Response
    {
        // Vérifier que la préinscription appartient à l'organisation de l'admin
        if ($preRegistration->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        $newStatus = $request->request->get('status');
        $notes = $request->request->get('notes');

        if (!in_array($newStatus, array_keys(PreRegistration::getAvailableStatuses()))) {
            $this->addFlash('error', 'Statut invalide');
            return $this->redirectToRoute('app_preregistration_show', ['id' => $preRegistration->getId()]);
        }

        $preRegistration->setStatus($newStatus);
        
        if ($notes) {
            $preRegistration->setNotes($notes);
        }

        if ($newStatus === PreRegistration::STATUS_CONTACTED) {
            $preRegistration->setContactedAt(new \DateTime());
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Statut mis à jour avec succès');

        return $this->redirectToRoute('app_preregistration_show', ['id' => $preRegistration->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_preregistration_delete', methods: ['POST'])]
    public function delete(PreRegistration $preRegistration, Request $request): Response
    {
        // Vérifier que la préinscription appartient à l'organisation de l'admin
        if ($preRegistration->getOrganization() !== $this->getUser()->getOrganization()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$preRegistration->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($preRegistration);
            $this->entityManager->flush();

            $this->addFlash('success', 'Préinscription supprimée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_preregistration_index');
    }
}