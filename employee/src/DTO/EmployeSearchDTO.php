<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeSearchDTO
{
    #[Assert\NotBlank(message: "El nombre no puede estar vacío.")]
    public $name;
}