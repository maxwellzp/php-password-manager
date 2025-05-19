<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Console\Style\SymfonyStyle;

class PasswordManagerService
{
    public function __construct(
        private readonly PasswordVault     $vault,
        private readonly EncryptionService $encryption
    )
    {
    }

    public function add(SymfonyStyle $io, string $key): void
    {
        $name = $io->ask('Entry name');
        $secret = $io->askHidden('Password to store');

        if (!$name || !$secret) {
            $io->error('Name and secret must not be empty.');
            return;
        }

        $encrypted = $this->encryption->encrypt($secret, $key);
        $this->vault->addEntry($name, $encrypted);
        $io->success("Entry '$name' added.");
    }

    public function get(SymfonyStyle $io, string $key): void
    {
        $name = trim($io->ask('Entry name'));
        $entry = $this->vault->getEntry($name);

        if ($entry) {
            try {
                $password = $this->encryption->decrypt($entry['ciphertext'], $entry['nonce'], $key);
                $io->success("Password for '$name': $password");
            } catch (\RuntimeException) {
                $io->error('Decryption failed.');
            }
        } else {
            $io->error('Entry not found.');
        }
    }

    public function delete(SymfonyStyle $io): void
    {
        $name = trim($io->ask('Entry name to delete'));
        if ($this->vault->deleteEntry($name)) {
            $io->success("Entry '$name' deleted.");
        } else {
            $io->error('Entry not found.');
        }
    }

    public function list(SymfonyStyle $io): void
    {
        $entries = $this->vault->listEntries();
        $io->listing($entries);
    }
}
