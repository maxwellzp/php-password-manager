<?php

declare(strict_types=1);

namespace App\Service;

class PasswordVault
{
    public function __construct(private string $filePath)
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException("$filePath file does not exist");
        }
    }

    public function loadVault(): array
    {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?? [];
    }

    public function saveVault(array $vaultData): void
    {
        $encoded = json_encode($vaultData, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $encoded);
    }
}
