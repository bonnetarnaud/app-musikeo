<?php

namespace App\Command;

use App\Entity\Admin;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create:admin',
    description: 'Create an admin user with organization',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Créer une organisation de test
        $organization = new Organization();
        $organization->setName('École de Musique Musikeo');
        $organization->setAddress('123 Rue de la Musique, 75001 Paris');
        $organization->setPhone('01 23 45 67 89');
        $organization->setEmail('contact@musikeo.com');

        $this->entityManager->persist($organization);

        // Créer un administrateur
        $admin = new Admin();
        $admin->setFirstname('Admin');
        $admin->setLastname('Musikeo');
        $admin->setEmail('admin@musikeo.com');
        $admin->setPassword(password_hash('admin123', PASSWORD_DEFAULT));
        $admin->setOrganization($organization);
        $admin->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Admin user created successfully!');
        $io->text([
            'Email: admin@musikeo.com',
            'Password: admin123',
            'Organization: École de Musique Musikeo'
        ]);

        return Command::SUCCESS;
    }
}
