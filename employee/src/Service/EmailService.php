<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class EmailService {
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $externalApiUrl
    )
    {
    }

    public function sendEmail(string $email, string $jwt): bool
    {
        try {
            $response = $this->httpClient->request('POST', $this->externalApiUrl, [
                'json' => [
                    'email' => $email
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwt
                ]
            ]);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                return true;
            }
        } catch (\Exception) {
            return false;
        }

        return false;
    }
}