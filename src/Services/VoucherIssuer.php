<?php

declare(strict_types = 1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;

class VoucherIssuer
{
    public function __construct(private Connection $connection) { }

    /**
     * @param string $sessionID
     *
     * @return array
     * @throws \Throwable
     */
    public function issue(string $sessionID): array
    {
        return $this->connection->transactional(
            function () use ($sessionID): array {
                $voucher = $this->connection
                    ->executeQuery(
                        $this->lockVoucherSQL(),
                        ['sessionID' => $sessionID]
                    )
                    ->fetchAssociative();

                if ($voucher === null) {
                    throw new OutOfVouchersException();
                }

                if ($voucher['session'] !== null) {
                    return $voucher;
                }

                $this->connection
                    ->update('vouchers', [
                        'session' => $sessionID,
                        'issued_at' => (new DateTimeImmutable())
                            ->format(DateTimeInterface::ATOM),
                    ], [
                        'id' => $voucher['id'],
                    ]);

                return $voucher;
            }
        );
    }

    protected function lockVoucherSQL(): string
    {
        return <<<SQL
            select * from vouchers
                where session = :sessionID or (session is null and issued_at is null)
                order by id
                limit 1
                for update
        SQL;
    }
}
