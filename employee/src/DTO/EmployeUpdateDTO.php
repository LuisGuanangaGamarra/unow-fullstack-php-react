<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeUpdateDTO extends EmployeDeleteDTO
{

    #[Assert\NotBlank(message: "El nombre no puede estar vacío.")]
    public $first_name;

    #[Assert\NotBlank(message: "El apellido no puede estar vacío.")]
    public $last_name;

    #[Assert\NotBlank(message: "La posicion no puede estar vacía")]
    public $position;
}