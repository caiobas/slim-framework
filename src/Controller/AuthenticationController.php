<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthenticationService;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthenticationController
{
    private AuthenticationService $authenticator;

    public function __construct(AuthenticationService $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @param Request $request
     */
    protected function authenticate(Request $request): int
    {
        $token = $request->getAttributes()['token'];
        $this->authenticator->authenticate($request, $token);
        return (int)$token['sub'];
    }
}
