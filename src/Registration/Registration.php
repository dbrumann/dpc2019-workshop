<?php declare(strict_types = 1);

namespace App\Registration;

use App\Registration\Object\RegistrationAttempt;
use App\Registration\Object\RegistrationRequest;
use function dd;
use Symfony\Component\Filesystem\Filesystem;
use function hash;
use function json_encode;
use function rtrim;
use function substr;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;

class Registration
{
    private $requestStorageDir;
    private $confirmationStorageDir;

    public function __construct(string $requestStorageDir, string $confirmationStorageDir)
    {
        $this->requestStorageDir = rtrim($requestStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->confirmationStorageDir = rtrim($confirmationStorageDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
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

    public function register(string $token, RegistrationAttempt $attempt): void
    {
        // TODO Save user

        // TODO Send confirmation email
    }
}
