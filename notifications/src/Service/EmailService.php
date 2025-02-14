<?php

namespace App\Service;

use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private readonly EmailServiceInterface $mailerProvider,
    )
    {
    }

    public function sendEmail(Email $email): void
    {
        $this->mailerProvider->sendEmail($email);
    }
}