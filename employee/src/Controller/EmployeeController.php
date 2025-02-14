<?php

namespace App\Controller;

use App\DTO\EmployeRegisterDTO;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtValidatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ValidationService;
use App\DTO\EmployeDeleteDTO;
use App\DTO\EmployeUpdateDTO;
use App\Entity\Employee;
use App\Service\EmailService;
use App\DTO\EmployeSearchDTO;

class EmployeeController extends AbstractController
{
    #[Route('/api/employees', name: 'employees_list', methods: ['GET'])]
    public function getEmployees(
        Request $request,
        JwtValidatorService $jwtValidator,
        EmployeeRepository $employeeRepository,
    ): Response
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalido o JWT expirado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $employees = $employeeRepository->findAll();
        $employeesScalarResult = array_map(function($employee) {
            return [
                'id' => $employee->getId(),
                'first_name' => $employee->getFirstName(),
                'last_name' => $employee->getLastName(),
                'position' => $employee->getPosition(),
                'birth_date' => $employee->getBirthday()->format('Y-m-d'),
            ];
        }, $employees);

        return new JsonResponse($employeesScalarResult, JsonResponse::HTTP_OK);
    }

    #[Route('/api/employees/{id}', name: 'employees_delete', methods: ['DELETE'])]
    public function deleteEmployee(
        Request $request,
        JwtValidatorService $jwtValidator,
        EmployeeRepository $employeeRepository,
        EntityManagerInterface $entityManager,
        ValidationService $validationService,
        int $id,
    ): Response
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalido o JWT expirado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $employeeDTO = new EmployeDeleteDTO();
        $employeeDTO->id = $id;
        $errors = $validationService->validate($employeeDTO);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $employee = $employeeRepository->find($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($employee);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/api/employees/{id}', name: 'employees_update', methods: ['PUT'])]
    public function updateEmployee(
        Request $request,
        JwtValidatorService $jwtValidator,
        EmployeeRepository $employeeRepository,
        EntityManagerInterface $entityManager,
        ValidationService $validationService,
        int $id,
    ): Response
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalido o JWT expirado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $request->getPayload();

        $employeeDTO = new EmployeUpdateDTO();
        $employeeDTO->id = $id;
        $employeeDTO->first_name = $data->get('first_name', null);
        $employeeDTO->last_name = $data->get('last_name', null);
        $employeeDTO->position = $data->get('position', null);

        $errors = $validationService->validate($employeeDTO);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $employee = $employeeRepository->find($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $employee->setFirstName($employeeDTO->first_name);
        $employee->setLastName($employeeDTO->last_name);
        $employee->setPosition($employeeDTO->position);

        $entityManager->persist($employee);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }

    #[Route('/api/employees', name: 'employees_create', methods: ['POST'])]
    public function createEmployee(
        Request $request,
        JwtValidatorService $jwtValidator,
        EntityManagerInterface $entityManager,
        ValidationService $validationService,
        EmailService $emailService,
    ): Response
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalido o JWT expirado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $request->getPayload();

        $employeeDTO = new EmployeRegisterDTO();
        $employeeDTO->first_name = $data->get('first_name', null);
        $employeeDTO->last_name = $data->get('last_name', null);
        $employeeDTO->position = $data->get('position', null);
        $employeeDTO->birth_date = $data->get('birth_date', null);
        $employeeDTO->email = $data->get('email', null);

        $errors = $validationService->validate($employeeDTO);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $employee = new Employee();
        $employee->setFirstName($employeeDTO->first_name);
        $employee->setLastName($employeeDTO->last_name);
        $employee->setPosition($employeeDTO->position);
        $employee->setBirthday(\DateTime::createFromFormat('Y-m-d', $employeeDTO->birth_date));

        $entityManager->persist($employee);
        $entityManager->flush();

        if(!$emailService->sendEmail($employeeDTO->email, $jwt)) {
            return new JsonResponse(['error' => 'Error al enviar el email'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/employees/search', name: 'employees_search', methods: ['GET'])]
    public function searchEmployee(
        Request $request,
        JwtValidatorService $jwtValidator,
        EmployeeRepository $employeeRepository,
        ValidationService $validationService,
    ): Response
    {
        $authorizationHeader = $request->headers->get('Authorization', '');

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalido o JWT expirado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $name = $request->query->get('name', '');

        $employeeDTO = new EmployeSearchDTO();
        $employeeDTO->name = $name;

        $errors = $validationService->validate($employeeDTO);
        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $employees = $employeeRepository->findByName($name);

        $employeesScalarResult = array_map(function($employee) {
            return [
                'id' => $employee->getId(),
                'first_name' => $employee->getFirstName(),
                'last_name' => $employee->getLastName(),
                'position' => $employee->getPosition(),
                'birth_date' => $employee->getBirthday()->format('Y-m-d'),
            ];
        }, $employees);

        return new JsonResponse($employeesScalarResult, JsonResponse::HTTP_OK);
    }
}