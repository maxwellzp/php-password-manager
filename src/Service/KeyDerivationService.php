<?php

declare(strict_types=1);

namespace App\Service;

class KeyDerivationService
{
    public function deriveKey(string $password, string $salt, int $keyLength = SODIUM_CRYPTO_SECRETBOX_KEYBYTES): string
    {
        return sodium_crypto_pwhash(
            $keyLength,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );
    }

    public function generateSalt(): string
    {
        return random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
    }
}
