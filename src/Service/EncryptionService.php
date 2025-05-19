<?php

declare(strict_types=1);

namespace App\Service;

class EncryptionService
{
    public function encrypt(string $plaintext, string $key): array
    {
        // Generate nonce. This should be unique for every encryption.
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Encrypt step.
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $key);

        // Binary data is base64-encoded so it can be safely stored in JSON.
        return [
            'nonce' => base64_encode($nonce),
            'ciphertext' => base64_encode($ciphertext),
        ];
    }

    public function decrypt(string $ciphertext, string $nonce, string $key): string
    {
        // Base64 decode the inputs
        $decodedCiphertext = base64_decode($ciphertext);
        $decodedNonce = base64_decode($nonce);

        // Attempt to decrypt
        $plaintext = sodium_crypto_secretbox_open($decodedCiphertext, $decodedNonce, $key);
        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed.');
        }

        // Return original secret
        return $plaintext;
    }
}