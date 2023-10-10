<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\StockController;
use App\Entity\StockQuote;
use App\Entity\User;
use App\Exception\StockNotValidException;
use App\Exception\UserNotFoundException;
use App\Response\StockResponse;
use App\Service\AuthenticationService;
use App\Service\StockService\StockService;
use Exception;
use JsonException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

/**
 * Class SignUpTest
 * @package Tests
 */
class StockTest extends BaseTestCase
{
    private StockService $stockService;
    private AuthenticationService $authenticatorService;

    private StockController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->stockService = $this->createMock(StockService::class);
        $this->authenticatorService = $this->createMock(AuthenticationService::class);
        $this->controller = new StockController($this->stockService, $this->authenticatorService);
    }

    /**
     * @throws JsonException
     */
    public function testStockEndpointSuccess(): void
    {
        // Arrange
        $token = [
            'sub' => '1',
            'email' => 'email@email.com',
            'iat' => 1234,
            'exp' => 12345
        ];
        $stockCode = 'wig';
        $request = $this->createRequest('GET', '/stock');
        $request = $request->withAttribute('token', $token)->withQueryParams(['q' => $stockCode]);

        $this->authenticatorService
            ->expects($this->once())
            ->method('authenticate')
            ->with($request, $token);

        $stock = (new StockQuote())
            ->setUser((new User())->setEmail('email@email.com'))
            ->setSymbol('Symbol')
            ->setName('Name')
            ->setHigh(100.5)
            ->setLow(100.1)
            ->setOpen(100.3)
            ->setClose(100.2);
        $stockResponse = new StockResponse($stock);

        $this->stockService
            ->expects($this->once())
            ->method('getStockQuote')
            ->with($stockCode, (int)$token['sub'])
            ->willReturn($stockResponse);

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);

        // Assert
        $this->assertEquals(json_encode($stockResponse->toArray(), JSON_THROW_ON_ERROR), (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testStockEndpointStockNotValidException(): void
    {
        // Arrange
        $token = [
            'sub' => '1',
            'email' => 'email@email.com',
            'iat' => 1234,
            'exp' => 12345
        ];
        $stockCode = 'not_valid';
        $request = $this->createRequest('GET', '/stock');
        $request = $request->withAttribute('token', $token)->withQueryParams(['q' => $stockCode]);

        $this->authenticatorService
            ->expects($this->once())
            ->method('authenticate')
            ->with($request, $token);

        $errorMsg = 'Stock ' . $stockCode . ' is not valid for search.';
        $this->stockService
            ->expects($this->once())
            ->method('getStockQuote')
            ->with($stockCode, (int)$token['sub'])
            ->willThrowException(new StockNotValidException($errorMsg));

        // Assert
        $this->expectException(HttpBadRequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage($errorMsg);

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);
    }

    /**
     * @throws JsonException
     */
    public function testStockEndpointUserNotFoundException(): void
    {
        // Arrange
        $token = [
            'sub' => '2',
            'email' => 'email@email.com',
            'iat' => 1234,
            'exp' => 12345
        ];
        $stockCode = 'wig';
        $request = $this->createRequest('GET', '/stock');
        $request = $request->withAttribute('token', $token)->withQueryParams(['q' => $stockCode]);

        $this->authenticatorService
            ->expects($this->once())
            ->method('authenticate')
            ->with($request, $token);

        $errorMsg = 'User not found.';
        $this->stockService
            ->expects($this->once())
            ->method('getStockQuote')
            ->with($stockCode, (int)$token['sub'])
            ->willThrowException(new UserNotFoundException($errorMsg));

        // Assert
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage($errorMsg);

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);
    }
}
