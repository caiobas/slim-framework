<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\EmailAlreadyRegistered;
use App\Exception\InvalidParametersException;
use App\Service\UserService;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class SignUpController
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
     * @throws \JsonException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $body = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
            $this->service->signUp($body);
        } catch (InvalidParametersException|EmailAlreadyRegistered|NotNullConstraintViolationException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }

        return $response->withStatus(201);
    }
}
