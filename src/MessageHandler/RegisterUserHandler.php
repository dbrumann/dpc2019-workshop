<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\RegisterUserMessage;
use const DIRECTORY_SEPARATOR;
use Doctrine\ORM\EntityManagerInterface;
use function rtrim;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RegisterUserHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $confirmationStorageDir;

    public function __construct(EntityManagerInterface $entityManager, string $confirmationStorageDir)
    {
        $this->entityManager = $entityManager;
        $this->confirmationStorageDir = rtrim($confirmationStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function __invoke(RegisterUserMessage $message): void
    {
        $this->entityManager->persist($message->getUser());
        $this->entityManager->flush();

        $filesystem = new Filesystem();
        $filesystem->remove($this->confirmationStorageDir . $message->getToken() . '.json');
    }
}
