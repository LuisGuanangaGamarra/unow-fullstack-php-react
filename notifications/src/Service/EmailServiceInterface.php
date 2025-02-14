<?php

namespace App\Service;

use Symfony\Component\Mime\Email;

interface EmailServiceInterface
{
    public function sendEmail(Email $email): void;
}
