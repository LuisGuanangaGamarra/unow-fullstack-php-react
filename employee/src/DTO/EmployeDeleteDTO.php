<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeDeleteDTO
{
    #[Assert\NotBlank(message: "El id no puede estar vacío.")]
    #[Assert\Positive(message: "El id debe ser un número positivo.")]
    public $id;
}