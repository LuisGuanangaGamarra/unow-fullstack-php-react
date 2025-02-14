<?php
namespace App\Controller;

use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtValidatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\EmailDTO;
use App\Service\ValidationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailController extends AbstractController
{
    public function __construct(
        private readonly EmailService $emailService,
        private readonly JwtValidatorService $jwtValidator,
        private readonly ValidationService $validationService,
        private readonly ParameterBagInterface $parameterBag,
    )
    {
    }

    #[Route('/api/notifications/send-mail', name: 'email', methods: ['POST'])]
    public function sendEmail(Request $request): Response
    {
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            return new JsonResponse(['error' => 'Invalid or expired JWT'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        if (!$this->jwtValidator->validateJwt($jwt)) {
            return new JsonResponse(['error' => 'Invalid or expired JWT'], JsonResponse::HTTP_UNAUTHORIZED);
        }


        $email = $request->getPayload()->get('email', null);
        $emailDTO = new EmailDTO();
        $emailDTO->email = $email;

        $errors = $this->validationService->validate($emailDTO);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $emailData = (new Email())
            ->from($this->parameterBag->get('MAILER_EMAIL'))
            ->to($email)
            ->subject('Nuevo mensaje')
            ->text('Este es un mensaje de prueba');

        $this->emailService->sendEmail($emailData);

        return new JsonResponse([
            'message' => 'Email enviado correctamente'
        ], Response::HTTP_OK);
    }
}