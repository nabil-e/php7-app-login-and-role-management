<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

final class MigrationUserToRole
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function migrate(): void
    {
        $schema = new Schema();
        $this->createUserToRoleTable($schema);

        $queries = $schema->toSql($this->connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    private function createUserToRoleTable(Schema $schema): void
    {
        $table = $schema->createTable('userToRole');
        $table->addColumn('ref_id', Type::BIGINT);
        $table->addColumn('user_id', Type::GUID);
        $table->addColumn('role_id', Type::BIGINT);
        $table->addForeignKeyConstraint('users', ['user_id'], ['user_id']);
        $table->addForeignKeyConstraint('roles', ['role_id'], ['role_id']);
    }
}