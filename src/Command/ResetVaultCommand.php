<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:reset-vault',
    description: 'Deletes the vault and salt files so you can reset your master password',
)]
class ResetVaultCommand extends Command
{
    public function __construct(
        #[Autowire(param: 'vault_file_path')]
        private readonly string $vaultFilePath,
        #[Autowire(param: 'salt_file_path')]
        private readonly string $saltFilePath,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $io->warning('This will permanently delete your vault and salt file.');
        if (!$io->confirm('Are you sure you want to reset the vault?', false)) {
            $io->text('Operation cancelled.');
            return Command::SUCCESS;
        }

        $vaultDeleted = @unlink($this->vaultFilePath);
        $saltDeleted = @unlink($this->saltFilePath);

        if ($vaultDeleted || $saltDeleted) {
            $io->success('Vault and salt reset. You can now use a new master password.');
        } else {
            $io->warning('No vault or salt file was found to delete.');
        }

        return Command::SUCCESS;
    }
}
