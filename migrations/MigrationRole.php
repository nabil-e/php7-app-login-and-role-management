<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

final class MigrationRole
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function migrate(): void
    {
        $schema = new Schema();
        $this->createRolesTable($schema);


        $queries = $schema->toSql($this->connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
        // insert Admin and SuperAdmin roles
        $this->connection->insert('roles', [
            'role_name' => 'SuperAdmin'
        ]);
        $this->connection->insert('roles', [
            'role_name' => 'Admin'
        ]);

    }

    private function down(Schema $schema): void
    {
        $schema->dropTable('roles');
    }

    private function createRolesTable(Schema $schema): void
    {   
            $table = $schema->createTable('roles');
            $table->addColumn('role_id', Type::INTEGER, ['autoincrement' => true]);
            $table->addColumn('role_name', Type::STRING, ['notnull' => true]);
    }
}