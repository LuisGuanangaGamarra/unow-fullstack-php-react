<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmailDTO
{
    #[Assert\NotBlank(message: "El email no puede estar vacío.")]
    #[Assert\Email(message: "El email no es válido.")]
    public $email;
}
