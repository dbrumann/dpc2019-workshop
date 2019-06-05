<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\RegisterUserMessage;
use App\Message\UserRegisteredMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RegisterUserHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public function __invoke(RegisterUserMessage $message): void
    {
        $this->entityManager->persist($message->getUser());

        $event = new UserRegisteredMessage($message->getToken(), $message->getUser());
        $eventMessage = (new Envelope($event))->with(new DispatchAfterCurrentBusStamp());

        $this->messageBus->dispatch($eventMessage);
    }
}
