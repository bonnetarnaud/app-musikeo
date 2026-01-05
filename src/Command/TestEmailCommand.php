<?php

namespace App\Command;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'app:test-email',
    description: 'Envoie un email de test pour vérifier la configuration du mailer',
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'L\'adresse email de destination')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipientEmail = $input->getArgument('email');

        try {
            $email = (new TemplatedEmail())
                ->from(new Address('noreply@musikeo.fr', 'Musikeo'))
                ->to($recipientEmail)
                ->subject('Test d\'envoi d\'email - Musikeo')
                ->htmlTemplate('emails/test.html.twig')
                ->context([
                    'test_date' => new \DateTime(),
                ])
            ;

            $this->mailer->send($email);

            $io->success(sprintf('Email de test envoyé avec succès à %s', $recipientEmail));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
