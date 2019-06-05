<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\RequestRegistrationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RequestRegistrationHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(RequestRegistrationMessage $message): string
    {
        $user = new User();

        $user->setName($message->getName());
        $user->setEmail($message->getEmail());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getToken();
    }
}
