<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\HistoryController;
use App\Entity\StockQuote;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Response\HistoryResponse;
use App\Response\StockResponse;
use App\Service\AuthenticationService;
use App\Service\UserService;
use Exception;
use JsonException;
use Slim\Exception\HttpNotFoundException;

/**
 * Class SignUpTest
 * @package Tests
 */
class HistoryTest extends BaseTestCase
{
    private UserService $userService;
    private AuthenticationService $authenticatorService;

    private HistoryController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->createMock(UserService::class);
        $this->authenticatorService = $this->createMock(AuthenticationService::class);
        $this->controller = new HistoryController($this->userService, $this->authenticatorService);
    }

    /**
     * @throws JsonException
     */
    public function testHistoryEndpointSuccess(): void
    {
        // Arrange
        $token = [
            'sub' => '1',
            'email' => 'email@email.com',
            'iat' => 1234,
            'exp' => 12345
        ];
        $request = $this->createRequest('GET', '/history');
        $request = $request->withAttribute('token', $token);

        $this->authenticatorService
            ->expects($this->once())
            ->method('authenticate')
            ->with($request, $token);

        $stocks = $this->generateStocks();
        $historyResponse = new HistoryResponse($stocks);

        $this->userService
            ->expects($this->once())
            ->method('history')
            ->with((int)$token['sub'])
            ->willReturn($historyResponse);

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);

        // Assert
        $this->assertEquals(json_encode($historyResponse->toArray(), JSON_THROW_ON_ERROR), (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testHistoryEndpointUserNotFoundException(): void
    {
        // Arrange
        $token = [
            'sub' => '1',
            'email' => 'email@email.com',
            'iat' => 1234,
            'exp' => 12345
        ];
        $request = $this->createRequest('GET', '/history');
        $request = $request->withAttribute('token', $token);

        $this->authenticatorService
            ->expects($this->once())
            ->method('authenticate')
            ->with($request, $token);

        $errorMsg = 'User not found.';
        $this->userService
            ->expects($this->once())
            ->method('history')
            ->with((int)$token['sub'])
            ->willThrowException(new UserNotFoundException($errorMsg));

        // Assert
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage($errorMsg);

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);
    }

    /**
     * @return array<StockResponse>
     */
    private function generateStocks(): array
    {
        $stocks = [];
        for($i = 0; $i < 3; $i++) {
            $stock = (new StockQuote())
                ->setUser((new User())->setEmail('email@email.com'))
                ->setSymbol('Symbol' . $i)
                ->setName('Name' . $i)
                ->setHigh(100.5 + $i)
                ->setLow(100.1 + $i)
                ->setOpen(100.3 + $i)
                ->setClose(100.2 + $i);

            $stocks[] = new StockResponse($stock);
        }

        return $stocks;
    }
}
