<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService 
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(string $to, string $subject, string $htmlTemplate, string $textTemplate, array $context = []): void
    {
        // Rendre les templates Twig avec les données
        $htmlContent = $this->twig->render($htmlTemplate, $context);
        $textContent = $this->twig->render($textTemplate, $context);

        $email = (new Email())
            ->from('test@cyanoalerte.com')
            ->to($to)
            ->subject($subject)
            ->text($textContent)
            ->html($htmlContent);

        $this->mailer->send($email);
    }
}