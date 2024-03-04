<?php

declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'migrate',
    description: 'Migrate the database to the latest revision.',
)]
final class MigrateCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Database Migrations');

        $this->runCommand(
            $output,
            'migrations:sync-metadata-storage'
        );

        if (
            0 === $this->runCommand(
                new NullOutput(),
                'migrations:up-to-date'
            )
        ) {
            $io->success('Database is already up to date!');
            return 0;
        }

        // Attempt DB migration.
        $io->section('Running database migrations...');

        $this->runCommand(
            $output,
            'migrations:migrate',
            [
                '--allow-no-migration' => true,
            ]
        );

        $io->newLine();
        $io->success('Database migration completed!');
        return 0;
    }
}
