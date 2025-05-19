<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SaltManagerService
{
    public function __construct(
        #[Autowire(param: 'salt_file_path')]
        private readonly string $saltFilePath,
        private readonly KeyDerivationService $keyDerivationService
    ) {}

    public function getOrCreateSalt(): string
    {
        if (!file_exists($this->saltFilePath)) {
            $salt = $this->keyDerivationService->generateSalt();
            file_put_contents($this->saltFilePath, base64_encode($salt));
            return $salt;
        }

        return base64_decode(file_get_contents($this->saltFilePath));
    }

    public function deriveKeyFromPassword(string $password): string
    {
        $salt = $this->getOrCreateSalt();
        return $this->keyDerivationService->deriveKey($password, $salt);
    }
}
