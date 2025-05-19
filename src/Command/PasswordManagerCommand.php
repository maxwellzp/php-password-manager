<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:password-manager',
    description: 'Secure CLI Password Manager',
)]
class PasswordManagerCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Secure Password Manager');

        $masterPassword = trim($io->askHidden('Enter your master password'));
        $io->info("Master password: $masterPassword");

        if (empty($masterPassword)) {
            $io->error('Master password cannot be empty.');
            return Command::FAILURE;
        }

        while(true) {
            $choice = $io->choice('Choose an action', ['Add', 'Get', 'Delete', 'List', 'Exit']);

           match ($choice) {
               'List' => fn() => "List",
               'Add' => fn() => "Add",
               'Get' => fn() => "Get",
               'Delete' => fn() => "Delete",
               'Exit' => fn() => "Exit",
           };

        }

        return Command::SUCCESS;
    }
}
