<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateRegistration(array $data): array
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(['max' => 180])
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 8]),
                new Assert\Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number'
                ])
            ],
            'firstName' => [
                new Assert\Optional([
                    new Assert\Length(['max' => 255])
                ])
            ],
            'lastName' => [
                new Assert\Optional([
                    new Assert\Length(['max' => 255])
                ])
            ]
        ]);

        $violations = $this->validator->validate($data, $constraints);
        
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }

    public function validateLogin(array $data): array
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email()
            ],
            'password' => [
                new Assert\NotBlank()
            ]
        ]);

        $violations = $this->validator->validate($data, $constraints);
        
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
} 