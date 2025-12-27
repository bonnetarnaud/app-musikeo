<?php

namespace App\Service;

use App\Entity\Admin;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createAdmin(
        string $email,
        string $firstName,
        string $lastName,
        string $password,
        Organization $organization
    ): Admin {
        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setFirstName($firstName);
        $admin->setLastName($lastName);
        $admin->setOrganization($organization);
        
        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        
        return $admin;
    }

    public function updatePassword(\App\Entity\User $user, string $newPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        
        $this->entityManager->flush();
    }

    public function getFullName(\App\Entity\User $user): string
    {
        return $user->getFirstName() . ' ' . $user->getLastName();
    }
}
