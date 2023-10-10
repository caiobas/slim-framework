<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\InvalidParametersException;
use App\Exception\UnauthenticatedException;
use App\Service\UserService;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class SignInController
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws JsonException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $body = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
            $token = json_encode($this->service->signIn($body)->toArray(), JSON_THROW_ON_ERROR);
        } catch (InvalidParametersException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (UnauthenticatedException $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }

        $response->getBody()->write($token);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
