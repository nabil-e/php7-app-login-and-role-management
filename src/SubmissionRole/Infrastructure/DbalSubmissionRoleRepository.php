<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Infrastructure;

use Doctrine\DBAL\Connection;
use WinYum\SubmissionRole\Domain\SubmissionRole;
use WinYum\SubmissionRole\Domain\SubmissionRoleRepository;

final class DbalSubmissionRoleRepository implements SubmissionRoleRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(SubmissionRole $submissionRole): void
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->insert('roles');
        $qb->values([
            'role_name' => $qb->createNamedParameter($submissionRole->getRoleName()),
        ]);

        $qb->execute();
    }
}