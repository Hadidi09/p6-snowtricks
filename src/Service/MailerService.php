<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailerService
{

    private $mailer;
    private $urlGenerator;
    private $twig;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function SendEmailVerification(string $email, string $token, string $user): void
    {
        $verificationUrl = $this->urlGenerator->generate(
            'app_verify_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('no-reply@snowtricks.com')
            ->to($email)
            ->subject('Valider votre adresse E-mail')
            ->html($this->twig->render('registration/confirmation_email.html.twig', [
                'tokenUrl' => $verificationUrl, 'user' => $user
            ]));

        $this->mailer->send($email);
    }
}
