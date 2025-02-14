<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\UserDTO;
use App\Service\ValidationService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthController extends AbstractController
{
    #[Route('/api/users/register', name: 'user_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTManager,
        EntityManagerInterface $entityManager,
        ValidationService $validationService,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userDTO = new UserDTO();
        $userDTO->email = $data['email'] ?? null;
        $userDTO->password = $data['password'] ?? null;
        $errors = $validationService->validate($userDTO);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($userRepository->findOneBy(['email' => $userDTO->email])) {
            return new JsonResponse(['error' => 'El usuario ya existe.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($userDTO->email);
        $encodedPassword = $passwordHasher->hashPassword(
            $user,
            $userDTO->password
        );
        $user->setPassword($encodedPassword);


        $entityManager->persist($user);
        $entityManager->flush();

        $token = $JWTManager->create($user);

        return new JsonResponse([
            'token' => $token
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/users/login', name: 'user_login', methods: ['POST'])]
    public function login(
        Request $request,
        JWTTokenManagerInterface $JWTManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ValidationService $validationService,
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $userDTO = new UserDTO();
        $userDTO->email = $data['email'] ?? null;
        $userDTO->password = $data['password'] ?? null;
        $errors = $validationService->validate($userDTO);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $userDTO->email]);

        if (!$user) {
            return new JsonResponse(['error' => 'El usuario no existe.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if(!$passwordHasher->isPasswordValid($user, $userDTO->password)) {
            return new JsonResponse(['error' => 'Credenciales inválidas.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = $JWTManager->create($user);
        return new JsonResponse([
            'token' => $token
        ]);
    }

    #[Route('/api/users/validate_token', name: 'user_validate', methods: ['GET'])]
    public function validateToken(TokenInterface $token): JsonResponse
    {
        return new JsonResponse(null, JsonResponse::HTTP_OK);
    }
}