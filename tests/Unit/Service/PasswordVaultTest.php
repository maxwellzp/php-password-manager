<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\PasswordVault;
use PHPUnit\Framework\TestCase;

class PasswordVaultTest extends TestCase
{
    private PasswordVault $passwordVault;
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testConstructWithCorrectPathDoesNotThrowException(): void
    {
        $filePath = __DIR__ . "/../../Fixtures/vault.json";

        $this->expectNotToPerformAssertions();

        $this->passwordVault = new PasswordVault($filePath);
    }

    public function testConstructWithIncorrectPathThrowsException(): void
    {
        $filePath = __DIR__ . "/../../NOT_CORRECT/vault.json";

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("$filePath file does not exist");

        $this->passwordVault = new PasswordVault($filePath);
    }

    public function testLoadVaultReturnsFileContent()
    {
        $filePath = __DIR__ . "/../../Fixtures/vault.json";
        $this->passwordVault = new PasswordVault($filePath);
        $content = $this->passwordVault->loadVault();

        $this->assertIsArray($content);
        $this->assertCount(2, $content);
    }

    public function testSaveVaultUpdateFileContent()
    {
        $filePath = __DIR__ . "/../../Fixtures/vault.json";
        $this->passwordVault = new PasswordVault($filePath);
        $vaultInMemory = [
            "testKey1" => "testValue1",
            "testKey2" => "testValue2",
        ];

        $this->passwordVault->saveVault($vaultInMemory);

        $vaultFromFile = $this->passwordVault->loadVault();
        foreach ($vaultInMemory as $key => $value) {
            $fileValue = $vaultFromFile[$key];
            $this->assertEquals($value, $fileValue);
        }
    }
}