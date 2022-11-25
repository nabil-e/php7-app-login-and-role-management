<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac;

use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Connection;
use WinYum\Framework\Rbac\Role\Admin;
use WinYum\Framework\Rbac\Role\SuperAdmin;
use WinYum\Framework\Rbac\Role\NewUser;
use Symfony\Component\HttpFoundation\Session\Session;

final class SymfonySessionCurrentUserFactory
{
    private $session;
    private $connection;

    public function __construct(Session $session, Connection $connection)
    {
        $this->session = $session;
        $this->connection = $connection;
    }

    public function create(): User
    {
        if (!$this->session->has('userId')) {
            return new Guest();
        }
        $roleName =  $this->findRoleWithUserId($this->session->get('userId'), $this->connection);
        $roleName = "WinYum\Framework\Rbac\Role\\{$roleName}";
        $roleName = new $roleName();        
        
        return new AuthenticatedUser(
            Uuid::fromString($this->session->get('userId')),
            [$roleName]
        );
    }

    private function convertStringToClassName(string $role)
    {
        $role = "WinYum\Framework\Rbac\Role\\{$role}";
        return new $role();
    }

    private function findRoleWithUserId(string $userId, Connection $connection): string
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('role_name');
        $qb->from('users');
        $qb->where("user_id = {$qb->createNamedParameter($userId)}");

        $stmt = $qb->execute();
        $rows = $stmt->fetchAll();

        $roleName = $rows[0]["role_name"];
        return $roleName;
    }
}
