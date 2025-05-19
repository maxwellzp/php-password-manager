<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PasswordVault
{
    private array $vault = [];
    public function __construct(
        #[Autowire(param: 'vault_file_path')]
        private readonly string $vaultPath,
        private readonly EncryptionService $encryptionService
    )
    {

    }

    public function loadVault(string $key): void
    {
        if (!file_exists($this->vaultPath)) {
            $this->vault = []; // fresh start
            return;
        }

        $data = json_decode(file_get_contents($this->vaultPath), true);
        if (!is_array($data) || !isset($data['ciphertext'], $data['nonce'])) {
            throw new \RuntimeException('Vault file is corrupted or unencrypted.');
        }

        $json = $this->encryptionService->decrypt($data['ciphertext'], $data['nonce'], $key);
        $this->vault = json_decode($json, true) ?? [];
    }

    public function saveVault(string $key): void
    {
        $json = json_encode($this->vault, JSON_PRETTY_PRINT);
        $encrypted = $this->encryptionService->encrypt($json, $key);
        file_put_contents($this->vaultPath, json_encode($encrypted, JSON_PRETTY_PRINT));
    }

    public function addEntry(string $label, array $encryptedData): void
    {
        $this->vault[$label] = $encryptedData;
    }

    public function getEntry(string $label): ?array
    {
        return $this->vault[$label] ?? null;
    }

    public function deleteEntry(string $label): bool
    {
        if (!isset($this->vault[$label])) {
            return false;
        }

        unset($this->vault[$label]);
        return true;
    }

    public function listEntries(): array
    {
        return array_keys($this->vault);
    }
}
