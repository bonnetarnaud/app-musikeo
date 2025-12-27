<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;

class OrganizationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createOrganization(
        string $name, 
        string $email, 
        string $type = 'school',
        string $subscriptionPlan = 'free'
    ): Organization {
        $organization = new Organization();
        $organization->setName($name);
        $organization->setType($type);
        $organization->setSubscriptionPlan($subscriptionPlan);
        $organization->setMaxStudents(30);
        $organization->setMaxTeachers(3);
        $organization->setMaxAdmins(1);
        $organization->setEmail($email);
        $organization->setActive(true);
        $organization->setCreatedAt(new \DateTime());
        
        // Générer un slug unique basé sur le nom
        $slug = $this->generateSlug($name);
        $organization->setSlug($slug);
        
        $this->entityManager->persist($organization);
        
        return $organization;
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        return $slug . '-' . uniqid();
    }

    public function canAddStudent(Organization $organization): bool
    {
        $currentStudents = $this->countStudents($organization);
        return $currentStudents < $organization->getMaxStudents();
    }

    public function canAddTeacher(Organization $organization): bool
    {
        $currentTeachers = $this->countTeachers($organization);
        return $currentTeachers < $organization->getMaxTeachers();
    }

    public function getUsageStats(Organization $organization): array
    {
        $currentStudents = $this->countStudents($organization);
        $currentTeachers = $this->countTeachers($organization);
        
        return [
            'students' => [
                'current' => $currentStudents,
                'max' => $organization->getMaxStudents(),
                'percentage' => $organization->getMaxStudents() > 0 
                    ? round(($currentStudents / $organization->getMaxStudents()) * 100, 2) 
                    : 0
            ],
            'teachers' => [
                'current' => $currentTeachers,
                'max' => $organization->getMaxTeachers(),
                'percentage' => $organization->getMaxTeachers() > 0 
                    ? round(($currentTeachers / $organization->getMaxTeachers()) * 100, 2) 
                    : 0
            ],
        ];
    }

    private function countStudents(Organization $organization): int
    {
        return $organization->getUsers()->filter(
            fn($user) => $user instanceof Student
        )->count();
    }

    private function countTeachers(Organization $organization): int
    {
        return $organization->getUsers()->filter(
            fn($user) => $user instanceof Teacher
        )->count();
    }
}
