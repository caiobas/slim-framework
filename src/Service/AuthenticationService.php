<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use JsonException;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthenticationService
{
    private string $secret;
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->secret = $_ENV['JWT_SECRET'];
    }

    /**
     */
    public function authenticate(Request $request, array $token): void
    {
        try {
            $id = (int)$token['sub'];
            $email = $token['email'];

            $user = $this->repository->getUserById($id);
            if($user->getEmail() !== $email){
                throw new HttpUnauthorizedException($request);
            }
        } catch(UserNotFoundException $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }
    }

    /**
     * @throws JsonException
     */
    public function generateToken(?User $user): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR);

        $payload = json_encode(
            [
                'sub' => (string)$user->getId(),
                'email' => $user->getEmail(),
                'iat' => (new \DateTime())->getTimestamp(),
                'exp' => (new \DateTime())->modify('+5 minutes')->getTimestamp()
            ]
            , JSON_THROW_ON_ERROR
        );

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
