<?php

declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'clear-cache',
    description: 'Clear all application caches.',
)]
final class ClearCacheCommand extends AbstractCommand
{
    public function __construct(
        private readonly AdapterInterface $cache,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Flush all cache entries.
        $this->cache->clear();

        $io->success('Local cache flushed.');
        return 0;
    }
}
