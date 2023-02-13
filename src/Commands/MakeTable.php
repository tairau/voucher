<?php

declare(strict_types = 1);

namespace App\Commands;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:table', description: 'Создать таблицу для работы с ваучерами')]
class MakeTable extends Command
{
    public const VOUCHERS_TABLE_NAME = 'vouchers';

    public function __construct(protected Connection $connection)
    {
        parent::__construct();
    }

    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $schema = $this->connection->createSchemaManager();

        if ($schema->tablesExist(static::VOUCHERS_TABLE_NAME)) {
            return static::FAILURE;
        }

        $table = new Table(
            name: static::VOUCHERS_TABLE_NAME,
            columns: [
                new Column('id', new BigIntType(), [
                    'unsigned'      => true,
                    'autoincrement' => true,
                ]),
                new Column('code', new StringType(), [
                    'length' => 10,
                ]),
                new Column('session', new StringType(), [
                    'length'  => 255,
                    'notnull' => false,
                ]),
                new Column('issued_at', new DateTimeType(), [
                    'notnull' => false,
                ]),
            ],
        );

        $table->addUniqueConstraint(['code']);
        $table->setPrimaryKey(['id']);

        $schema->createTable($table);

        return static::SUCCESS;
    }
}
