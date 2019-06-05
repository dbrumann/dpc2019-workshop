<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\RegisterUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterUserHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(RegisterUserMessage $message): void
    {
        $this->entityManager->persist($message->getUser());
        $this->entityManager->flush();
    }
}
