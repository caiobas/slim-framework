<?php

declare(strict_types=1);

namespace App\Queue\Email\Template;

class EmailFromTemplate
{
    private string $email;
    private string $name;

    /**
     * @param string $email
     * @param string $name
     */
    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
