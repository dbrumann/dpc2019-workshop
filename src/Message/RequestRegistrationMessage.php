<?php declare(strict_types = 1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

class RequestRegistrationMessage
{
    /**
     * @Assert\NotNull()
     * @Assert\Length(min=6, max=100)
     */
    public $name;

    /**
     * @Assert\NotNull()
     * @Assert\Email()
     */
    public $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
