<?php declare(strict_types = 1);

namespace App\Command;

use App\Registration\Registration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use function json_decode;
use function rtrim;
use function sprintf;
use const DIRECTORY_SEPARATOR;
use Symfony\Component\Routing\RouterInterface;

class ConfirmRegistrationRequestsCommand extends Command
{
    private $requestStorageDir;
    private $registration;
    private $router;

    public function __construct(string $requestStorageDir, Registration $registration, RouterInterface $router)
    {
        parent::__construct();

        $this->requestStorageDir = rtrim($requestStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->registration = $registration;
        $this->router = $router;
    }

    protected function configure()
    {
        $this
            ->setName('registration:confirm')
            ->setDescription('Confirm registration requests.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $finder = (new Finder())->in($this->requestStorageDir)->files();

        $io->title('Confirm registration requests');

        foreach ($finder->getIterator() as $file) {
            $request = json_decode($file->getContents(), true);

            $io->section(sprintf('Request received: %s', $request['createdAt']['date']));
            $confirmed = $io->confirm(
                sprintf('Do you want to confirm registration for %s <%s>', $request['name'], $request['email'])
            );

            if ($confirmed) {
                $token = $this->registration->confirm($file->getPathname());
                $url = $this->router->generate('registration_register', ['token' => $token], RouterInterface::ABSOLUTE_URL);

                $io->success('User registration confirmed.');
                $io->note($url);
            }
        }

        $io->success('Finished processing all registration requests');
    }
}
