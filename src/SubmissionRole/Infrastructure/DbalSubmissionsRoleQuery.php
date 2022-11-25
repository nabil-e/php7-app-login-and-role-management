<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Infrastructure;

use Doctrine\DBAL\Connection;
use WinYum\SubmissionRole\Application\SubmissionRole;
use WinYum\SubmissionRole\Application\SubmissionsRoleQuery;


final class DbalSubmissionsRoleQuery implements SubmissionsRoleQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('role_name');
        $qb->from('roles');

        $stmt = $qb->execute();
        $rows = $stmt->fetchAll();

        $roles = [];
        foreach ($rows as $row) {
            $roles[] = new SubmissionRole($row['role_name']);
        }
        return $roles;
    }

    
}