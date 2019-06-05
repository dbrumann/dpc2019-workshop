<?php declare(strict_types = 1);

namespace App\Message;

class UserRegisteredMessage
{
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
