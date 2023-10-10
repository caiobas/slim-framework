<?php

declare(strict_types=1);

use Slim\App;
use Slim\Exception\HttpUnauthorizedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Tuupola\Middleware\JwtAuthentication;

return static function (App $app) {
    $app->add(new JwtAuthentication([
        "path" => ["/stock", "/history"], // protected routes
        "secret" => $_ENV['JWT_SECRET'],
        "error" => function ($response, $args) {
            return $response->withStatus(401);
        }
    ]));

    $app->add(function (Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);
        $statusCode = $response->getStatusCode();

        // Throw if invalid token
        if ($statusCode == 401) {
            throw new HttpUnauthorizedException($request);
        }

        return $response;
    });
};
