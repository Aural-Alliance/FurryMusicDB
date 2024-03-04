<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Environment;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'uptime-wait',
    description: 'Wait for related services to be up before continuing.',
)]
final class UptimeWaitCommand extends AbstractCommand
{
    public function __construct(
        private readonly Environment $environment
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Waiting on services...');

        $parser = new DsnParser([
            'postgres' => 'pdo_pgsql',
        ]);
        $connectionOptions = $parser->parse($this->environment->getDatabaseUrl());

        $elapsed = 0;
        $timeout = 180;

        while ($elapsed <= $timeout) {
            try {
                $conn = DriverManager::getConnection($connectionOptions);
                $pdo = $conn->getNativeConnection();

                assert($pdo instanceof \PDO);

                $pdo->exec('SELECT 1');

                $io->success('Services started up and ready!');
                return 0;
            } catch (\Throwable $e) {
                sleep(1);
                $elapsed += 1;

                $io->writeln($e->getMessage());
            }
        }

        $io->error('Timed out waiting for services to start.');
        return 1;
    }
}
