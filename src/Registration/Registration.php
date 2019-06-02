<?php declare(strict_types = 1);

namespace App\Registration;

use App\Entity\User;
use App\Registration\Object\RegistrationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use function hash;
use function json_encode;
use function rtrim;
use function substr;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;

final class Registration
{
    private $requestStorageDir;
    private $confirmationStorageDir;
    private $entityManager;

    public function __construct(string $requestStorageDir, string $confirmationStorageDir, EntityManagerInterface $entityManager)
    {
        $this->requestStorageDir = rtrim($requestStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->confirmationStorageDir = rtrim($confirmationStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->entityManager = $entityManager;
    }

    public function request(RegistrationRequest $request): void
    {
        $filesystem = new Filesystem();
        $filename = $request->createdAt->format('U') . '.json';

        $filesystem->dumpFile($this->requestStorageDir . $filename, json_encode($request, JSON_PRETTY_PRINT));
    }

    public function confirm(string $requestFilename): string
    {
        $filesystem = new Filesystem();
        $filename = hash('crc32', $requestFilename) . '.json';

        $filesystem->copy($requestFilename, $this->confirmationStorageDir . $filename);
        $filesystem->remove($requestFilename);

        return substr($filename, 0, -5);
    }

    public function register(string $token, User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $filesystem = new Filesystem();
        $filesystem->remove($this->confirmationStorageDir . $token . '.json');

        // TODO Send confirmation email
    }
}
