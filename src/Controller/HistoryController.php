<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserNotFoundException;
use App\Service\AuthenticationService;
use App\Service\UserService;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class HistoryController extends AuthenticationController
{
    private UserService $service;

    public function __construct(UserService $service, AuthenticationService $authenticator)
    {
        parent::__construct($authenticator);
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
        $id = $this->authenticate($request);
        try {
            $history = $this->service->history($id)->toArray();
            $body = json_encode($history, JSON_THROW_ON_ERROR);
            $response->getBody()->write($body);
        } catch (UserNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
