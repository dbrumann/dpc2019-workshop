<?php declare(strict_types = 1);

namespace App\Message;

use App\Entity\User;

class UserRegisteredMessage
{
    private $token;
    private $user;

    public function __construct(string $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
