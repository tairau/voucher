<?php

declare(strict_types = 1);

namespace App\Commands;

use App\Contracts\Randomizer;
use Doctrine\DBAL\Connection;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'seed:vouchers', description: 'Создать случайные ваучеры')]
class SeedVouchers extends Command
{
    private const VOUCHERS_LIMIT = 500_000;

    public function __construct(
        protected Connection $connection,
        protected Randomizer $randomizer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output, static::VOUCHERS_LIMIT);
        $values = [];
        $batch = 10_000;
        $progressBar->start();

        for ($i = 1; $i <= static::VOUCHERS_LIMIT; $i++) {
            $values[] = "('{$this->randomizer->make()}')";

            if ($i % $batch === 0) {
                try {
                    $this->insertBatch($values);
                    $progressBar->advance($batch);
                } catch (Exception) {
                    $i = $i - $batch;
                }
                finally {
                    $values = [];
                }
            }
        }

        $progressBar->finish();

        return static::SUCCESS;
    }

    protected function insertBatch(array $values)
    {
        $values = implode(',', $values);

        $sql = <<<SQL
            insert into vouchers (code) values $values
        SQL;

        $this->connection->executeStatement($sql);
    }
}
