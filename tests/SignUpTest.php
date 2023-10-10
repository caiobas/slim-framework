<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\SignUpController;
use App\Exception\EmailAlreadyRegistered;
use App\Exception\InvalidParametersException;
use App\Service\UserService;
use Exception;
use JsonException;
use Slim\Exception\HttpBadRequestException;

/**
 * Class SignUpTest
 * @package Tests
 */
class SignUpTest extends BaseTestCase
{
    private UserService $service;

    private SignUpController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->createMock(UserService::class);
        $this->controller = new SignUpController($this->service);
    }

    /**
     * @throws JsonException
     */
    public function testSignUpEndpointSuccess(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com",
            "password": "123456",
            "first_name": "first",
            "last_name": "last"
        }';
        $request = $this->createRequest('POST', '/sign-up', $body);


        $this->service
            ->expects($this->once())
            ->method('signUp')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR));

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);

        // Assert
        $this->assertEquals("", (string) $response->getBody());
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testSignUpEndpointEmailAlreadyRegistered(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com",
            "password": "123456",
            "first_name": "first",
            "last_name": "last"
        }';
        $request = $this->createRequest('POST', '/sign-up', $body);

        $errorMsg = 'Email already registered.';
        $this->service
            ->expects($this->once())
            ->method('signUp')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR))
            ->willThrowException(new EmailAlreadyRegistered($errorMsg));

        // Assert
        $this->expectException(HttpBadRequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage($errorMsg);

        // Act
        ($this->controller)($request, $this->createResponse(), []);
    }

    /**
     * @throws JsonException
     */
    public function testSignUpEndpointInvalidParametersException(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com",
            "password": "123456",
            "first_name": "first"
        }';
        $request = $this->createRequest('POST', '/sign-up', $body);

        $errorMsg = 'App\\Entity\\User::setLastName(): Argument #1 ($lastName) must be of type string, null given, called in /var/www/slim_app/src/Service/UserService.php on line 42';
        $this->service
            ->expects($this->once())
            ->method('signUp')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR))
            ->willThrowException(new InvalidParametersException($errorMsg));

        // Assert
        $this->expectException(HttpBadRequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage($errorMsg);

        // Act
        ($this->controller)($request, $this->createResponse(), []);
    }
}
