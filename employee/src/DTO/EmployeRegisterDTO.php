<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeRegisterDTO
{
    #[Assert\NotBlank(message: "El nombre no puede estar vacío.")]
    public $first_name;

    #[Assert\NotBlank(message: "El apellido no puede estar vacío.")]
    public $last_name;

    #[Assert\NotBlank(message: "La posicion no puede estar vacía")]
    public $position;

    #[Assert\NotBlank(message: "La fecha de nacimiento no puede estar vacia")]
    #[Assert\Date(message: "La fecha de nacimiento no es válida")]
    public $birth_date;

    #[Assert\NotBlank(message: "El email no puede estar vacío.")]
    #[Assert\Email(message: "El email no es válido.")]
    public $email;
}