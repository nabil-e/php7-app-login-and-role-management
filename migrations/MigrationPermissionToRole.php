<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

final class MigrationPermissionToRole
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function migrate(): void
    {
        $schema = new Schema();
        $this->createPermissionToRolesTable($schema);

        $queries = $schema->toSql($this->connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    private function createPermissionToRolesTable(Schema $schema): void
    {
        $table = $schema->createTable('permissionToRoles');
        $table->addColumn('ref_id', Type::BIGINT);
        $table->addColumn('role_id', Type::BIGINT);
        $table->addColumn('permission_id', Type::BIGINT);
        $table->addForeignKeyConstraint('roles', ['role_id'], ['role_id']);
        $table->addForeignKeyConstraint('permissions', ['permission_id'], ['permission_id']);
    }
}