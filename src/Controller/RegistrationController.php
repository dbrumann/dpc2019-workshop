<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Message\RegisterUserMessage;
use App\Message\RequestRegistrationMessage;
use App\Registration\Form\RegistrationAttemptType;
use App\Registration\Form\RegistrationRequestType;
use App\Registration\Object\RegistrationRequest;
use App\Registration\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(name="registration_")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/", name="request")
     */
    public function requestRegistration(Request $request, Registration $registration): Response
    {
        $form = $this->createForm(RegistrationRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationRequest $registrationRequest */
            $registrationRequest = $form->getData();

            $envelope = $this->dispatchMessage(new RequestRegistrationMessage($registrationRequest->name, $registrationRequest->email));
            $token = $envelope->last(HandledStamp::class)->getResult();

            return $this->redirectToRoute('registration_register', ['token' => $token]);
        }

        return $this->render(
            'registration/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/confirm", name="confirmation")
     */
    public function confirmRequest(): Response
    {
        return $this->render('registration/confirm.html.twig');
    }

    /**
     * @Route("/register/{token}", name="register")
     */
    public function register(
        string $token,
        Request $request,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = $entityManager->getRepository(User::class)->findOneByToken($token);
        if (!$user) {
            throw $this->createNotFoundException('Could not find a user for the provided token.');
        }

        $form = $this->createForm(RegistrationAttemptType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $messageBus->dispatch(new RegisterUserMessage($token, $user));

            return $this->redirectToRoute('registration_success');
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/success", name="success")
     */
    public function confirmRegistration(): Response
    {
        return $this->render('registration/success.html.twig');
    }
}
