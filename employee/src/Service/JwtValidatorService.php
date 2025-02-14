<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class JwtValidatorService {
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $externalApiUrl
    )
    {
    }

    public function validateJwt(string $jwt): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->externalApiUrl, [
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