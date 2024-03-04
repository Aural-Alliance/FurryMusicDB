<?php

declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'init',
    description: 'Initialize the application on container startup.',
)]
final class InitCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runCommand(
            $output,
            'uptime-wait'
        );

        $this->runCommand(
            $output,
            'clear-cache'
        );

        $this->runCommand(
            $output,
            'migrate'
        );

        $this->runCommand(
            $output,
            'orm:generate-proxies'
        );

        return 0;
    }
}
