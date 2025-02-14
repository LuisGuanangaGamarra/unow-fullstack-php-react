<?php
namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($dto): array
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $errorMessages;
        }
        return [];
    }
}