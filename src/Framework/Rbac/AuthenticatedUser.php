<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac;

use Ramsey\Uuid\UuidInterface;
use WinYum\Framework\Rbac\Role;

final class AuthenticatedUser implements User
{
    private $id;
    private $roles = [];

    /**
     * @param Role[] $roles
     */
    public function __construct(UuidInterface $id, array $roles)
    {
        $this->id = $id;
        $this->roles = $roles;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }



    public function hasPermission(Permission $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    private function my_autoloader($role)
    {
        stream_resolve_include_path(__NAMESPACE__ .'\Role\\'.$role.'.php') ;
    }
    
    public function retrievePermission($role): array
    {
        $this->my_autoloader($role);
        $role = 'WinYum\Framework\Rbac\Role\\'.$role;
        $role = new $role();
        return $role->retrievePermission();
    }
}