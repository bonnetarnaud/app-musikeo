<?php

namespace App\Service;

use App\Entity\PreRegistration;
use Doctrine\ORM\EntityManagerInterface;

class PreRegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailService $emailService
    ) {
    }

    public function updateStatus(PreRegistration $preRegistration, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $preRegistration->getStatus();
        $preRegistration->setStatus($newStatus);
        
        if ($notes) {
            $currentNotes = $preRegistration->getNotes() ?? '';
            $timestamp = (new \DateTime())->format('Y-m-d H:i');
            $preRegistration->setNotes($currentNotes . "\n[$timestamp] " . $notes);
        }
        
        $this->entityManager->flush();
        
        // Envoyer un email selon le nouveau statut
        $this->sendStatusChangeEmail($preRegistration, $oldStatus, $newStatus);
    }

    private function sendStatusChangeEmail(PreRegistration $preRegistration, string $oldStatus, string $newStatus): void
    {
        // Envoyer un email quand le statut change vers "contacted" ou "enrolled"
        if ($newStatus === PreRegistration::STATUS_CONTACTED) {
            $this->emailService->sendPreRegistrationContactedEmail(
                $preRegistration->getEmail(),
                $preRegistration->getStudentFirstName(),
                $preRegistration->getOrganization()->getName()
            );
        } elseif ($newStatus === PreRegistration::STATUS_ENROLLED) {
            $this->emailService->sendPreRegistrationEnrolledEmail(
                $preRegistration->getEmail(),
                $preRegistration->getStudentFirstName(),
                $preRegistration->getOrganization()->getName()
            );
        }
    }

    public function convertToStudent(PreRegistration $preRegistration): void
    {
        // TODO: Créer un vrai étudiant à partir de la pré-inscription
        $preRegistration->setStatus(PreRegistration::STATUS_ENROLLED);
        $this->entityManager->flush();
    }
}
