<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotNull()
     * @Assert\Length(max=100)
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @Assert\NotNull()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @Assert\NotNull()
     *
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        // No salt needed with modern hashing algorithms.

        return null;
    }

    public function eraseCredentials(): void
    {
        // Intentionally left blank. No temporary credentials are stored in this entity.
    }
}
