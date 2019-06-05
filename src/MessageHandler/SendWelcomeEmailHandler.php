<?php declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\UserRegisteredMessage;
use function sprintf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendWelcomeEmailHandler implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(UserRegisteredMessage $message): void
    {
        $user = $message->getUser();
        $email = new Email();
        $email
            ->from('workshop-demo@example.com')
            ->to($user->getEmail())
            ->subject('Your account was successfully created')
            ->text(sprintf('Welcome %s! You can now log in using your email-address: %s', $user->getName(), $user->getEmail()));

        $this->mailer->send($email);
    }
}
