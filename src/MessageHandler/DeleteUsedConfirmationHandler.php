<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\UserRegisteredMessage;
use const DIRECTORY_SEPARATOR;
use function rtrim;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeleteUsedConfirmationHandler implements MessageHandlerInterface
{
    private $confirmationStorageDir;

    public function __construct(string $confirmationStorageDir)
    {
        $this->confirmationStorageDir = rtrim($confirmationStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function __invoke(UserRegisteredMessage $message): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->confirmationStorageDir . $message->getToken() . '.json');
    }
}
