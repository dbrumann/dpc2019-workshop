<?php declare(strict_types = 1);

namespace App\Registration\Object;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationRequest
{
    /**
     * @Assert\NotNull()
     * @Assert\Length(max=100)
     */
    public $name;

    /**
     * @Assert\NotNull()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\Type(type="DateTimeImmutable")
     */
    public $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
