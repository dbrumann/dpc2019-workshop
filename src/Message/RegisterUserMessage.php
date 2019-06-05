<?php declare(strict_types = 1);

namespace App\Message;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserMessage
{
    /**
     * @Assert\NotNull()
     * @Assert\Length()
     */
    private $token;

    /**
     * @Assert\Valid()
     */
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
