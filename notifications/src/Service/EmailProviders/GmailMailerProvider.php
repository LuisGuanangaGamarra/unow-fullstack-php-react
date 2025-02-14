<?php

namespace App\Service\EmailProviders;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use App\Service\EmailServiceInterface;
use Symfony\Component\Mailer\MailerInterface;

class GmailMailerProvider implements EmailServiceInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function sendEmail(Email $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
        }
    }
}