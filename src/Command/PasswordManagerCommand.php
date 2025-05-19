<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PasswordManagerService;
use App\Service\PasswordVault;
use App\Service\SaltManagerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:password-manager',
    description: 'Secure CLI Password Manager',
)]
class PasswordManagerCommand extends Command
{
    public function __construct(
        private readonly PasswordVault $passwordVault,
        private readonly PasswordManagerService $service,
        private readonly SaltManagerService $saltManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🔐 Password Manager');

        $masterPassword = trim($io->askHidden('Enter your master password'));

        if (empty($masterPassword)) {
            $io->error('Master password cannot be empty.');
            return Command::FAILURE;
        }

        $encryptionKey = $this->saltManager->deriveKeyFromPassword($masterPassword);

        try {
            $this->passwordVault->loadVault($encryptionKey);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        while (true) {
            $choice = $io->choice('Choose an action', ['Add', 'Get', 'Delete', 'List', 'Exit']);

            if ($choice === 'Exit') {
                break;
            }

            match ($choice) {
                'Add' => $this->service->add($io, $encryptionKey),
                'Get' => $this->service->get($io, $encryptionKey),
                'Delete' => $this->service->delete($io),
                'List' => $this->service->list($io),
            };
        }

        try {
            $this->passwordVault->saveVault($encryptionKey);
        } catch (\Exception $e) {
            $io->error('Failed to save vault: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Vault saved and application exited.');
        return Command::SUCCESS;
    }
}
