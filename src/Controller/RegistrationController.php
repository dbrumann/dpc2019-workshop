<?php declare(strict_types = 1);

namespace App\Controller;

use App\Registration\Form\RegistrationAttemptType;
use App\Registration\Form\RegistrationRequestType;
use App\Registration\Object\RegistrationAttempt;
use App\Registration\Registration;
use const DIRECTORY_SEPARATOR;
use function file_get_contents;
use function json_decode;
use function rtrim;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $registration->request($form->getData());

            return $this->redirectToRoute('registration_confirmation');
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
    public function register(string $token, Request $request, Registration $registration, string $confirmationStorageDir): Response
    {
        $filesystem = new Filesystem();
        $confirmationStorageDir = rtrim($confirmationStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filename = $confirmationStorageDir . $token . '.json';

        if (! $filesystem->exists($filename)) {
            throw $this->createNotFoundException();
        }
        $registrationAttempt = $this->createRegistrationAttempt($filename);

        $form = $this->createForm(RegistrationAttemptType::class, $registrationAttempt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration->register($token, $form->getData());

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

    private function createRegistrationAttempt(string $filename): RegistrationAttempt
    {
        $data = json_decode(file_get_contents($filename), true);

        $attempt = new RegistrationAttempt();
        $attempt->name = $data['name'];
        $attempt->email = $data['email'];

        return $attempt;
    }
}
