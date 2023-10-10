<?php

declare(strict_types=1);

namespace App\Response;

class SignInResponse  {

    private string $token;
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function toArray(): array
    {
        return ['token' => $this->token];
    }
}
