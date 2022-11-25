<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac;

use WinYum\Framework\Rbac\Permission;

abstract class Role
{
    public function hasPermission(Permission $permission): bool
    {
        return in_array($permission, $this->getPermissions(), false);
    }

    /**
     * @return Permission[]
     */
    abstract protected function getPermissions(): array;
}