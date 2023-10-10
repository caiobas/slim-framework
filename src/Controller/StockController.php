<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\StockNotValidException;
use App\Exception\UserNotFoundException;
use App\Service\AuthenticationService;
use App\Service\StockService\StockService;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class StockController extends AuthenticationController
{
    private StockService $service;

    public function __construct(StockService $service, AuthenticationService $authenticator)
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
            $stockCode = $request->getQueryParams()['q'];
            $stock = $this->service->getStockQuote($stockCode, $id)->toArray();
            $body = json_encode($stock, JSON_THROW_ON_ERROR);
            $response->getBody()->write($body);
        } catch (StockNotValidException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (UserNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
