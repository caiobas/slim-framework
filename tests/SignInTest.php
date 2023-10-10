<?php

declare(strict_types=1);

namespace Tests;

use App\Controller\SignInController;
use App\Exception\InvalidParametersException;
use App\Exception\UnauthenticatedException;
use App\Response\SignInResponse;
use App\Service\UserService;
use Exception;
use JsonException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

/**
 * Class SignUpTest
 * @package Tests
 */
class SignInTest extends BaseTestCase
{
    private UserService $service;

    private SignInController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->createMock(UserService::class);
        $this->controller = new SignInController($this->service);
    }

    /**
     * @throws JsonException
     */
    public function testSignInEndpointSuccess(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com",
            "password": "123456"
        }';
        $request = $this->createRequest('POST', '/sign-in', $body);


        $this->service
            ->expects($this->once())
            ->method('signIn')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR))
            ->willReturn(new SignInResponse('jwt_token'));

        // Act
        $response = ($this->controller)($request, $this->createResponse(), []);

        // Assert
        $this->assertEquals('{"token":"jwt_token"}', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testSignInEndpointUnauthenticatedException(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com",
            "password": "wrong_pass"
        }';
        $request = $this->createRequest('POST', '/sign-in', $body);

        $errorMsg = 'Email or password is incorrect.';
        $this->service
            ->expects($this->once())
            ->method('signIn')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR))
            ->willThrowException(new UnauthenticatedException($errorMsg));

        // Assert
        $this->expectException(HttpUnauthorizedException::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage($errorMsg);

        // Act
        ($this->controller)($request, new Response(), []);
    }

    /**
     * @throws JsonException
     */
    public function testSignInEndpointInvalidParametersException(): void
    {
        // Arrange
        $body = '{
            "email": "email@email.com"
        }';
        $request = $this->createRequest('POST', '/sign-in', $body);

        $errorMsg = 'crypt(): Argument #1 ($string) must be of type string, null given';
        $this->service
            ->expects($this->once())
            ->method('signIn')
            ->with(json_decode($body, false, 512, JSON_THROW_ON_ERROR))
            ->willThrowException(new InvalidParametersException($errorMsg));

        // Assert
        $this->expectException(HttpBadRequestException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage($errorMsg);

        // Act
        ($this->controller)($request, new Response(), []);
    }
}
