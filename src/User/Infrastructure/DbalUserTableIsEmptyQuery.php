<?php

declare(strict_types=1);

namespace WinYum\User\Infrastructure;

use Doctrine\DBAL\Connection;
use WinYum\User\Application\UserTableIsEmptyQuery;

/**
 * Bypass permission when user table is empty.
 * for creatting the first user.
 */

final class DbalUserTableIsEmptyQuery implements UserTableIsEmptyQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(): bool
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('count(*)');
        $qb->from('users');
        $qb->execute();

        $stmt = $qb->execute();
        return (bool)$stmt->fetchOne();
    }
}
