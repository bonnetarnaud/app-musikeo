<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {
    }

    public function sendWelcomeEmail(string $to, string $firstName, string $organizationName): void
    {
        $email = (new Email())
            ->from('noreply@musikeo.com')
            ->to($to)
            ->subject('Bienvenue sur Musikeo !')
            ->html($this->twig->render('emails/welcome.html.twig', [
                'firstName' => $firstName,
                'organizationName' => $organizationName
            ]));

        $this->mailer->send($email);
    }

    public function sendPreRegistrationConfirmation(string $to, string $studentFirstName, string $organizationName): void
    {
        $email = (new Email())
            ->from('noreply@musikeo.com')
            ->to($to)
            ->subject('Confirmation de votre pré-inscription')
            ->html($this->twig->render('emails/preregistration_confirmation.html.twig', [
                'studentFirstName' => $studentFirstName,
                'organizationName' => $organizationName
            ]));

        $this->mailer->send($email);
    }

    public function sendPreRegistrationContactedEmail(string $to, string $studentFirstName, string $organizationName): void
    {
        $email = (new Email())
            ->from('noreply@musikeo.com')
            ->to($to)
            ->subject('Nous avons bien reçu votre demande')
            ->html($this->twig->render('emails/preregistration_contacted.html.twig', [
                'studentFirstName' => $studentFirstName,
                'organizationName' => $organizationName
            ]));

        $this->mailer->send($email);
    }

    public function sendPreRegistrationEnrolledEmail(string $to, string $studentFirstName, string $organizationName): void
    {
        $email = (new Email())
            ->from('noreply@musikeo.com')
            ->to($to)
            ->subject('Inscription confirmée !')
            ->html($this->twig->render('emails/preregistration_enrolled.html.twig', [
                'studentFirstName' => $studentFirstName,
                'organizationName' => $organizationName
            ]));

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(string $to, string $token): void
    {
        $email = (new Email())
            ->from('noreply@musikeo.com')
            ->to($to)
            ->subject('Réinitialisation de votre mot de passe')
            ->html($this->twig->render('emails/password_reset.html.twig', [
                'resetToken' => $token
            ]));

        $this->mailer->send($email);
    }
}

