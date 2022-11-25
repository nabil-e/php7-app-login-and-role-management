<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac\Role;

use WinYum\Framework\Rbac\Role;
use WinYum\Framework\Rbac\Permission\SubmitLink;
use WinYum\Framework\Rbac\Permission\AddAuthAccessToResource;

final class AccessManager extends Role
{
    /**
     * @return \WinYum\Framework\Rbac\Permission
     */
    protected function getPermissions(): array
    {
        return [
            new AddAuthAccessToResource(),
            new SubmitLink(),
        ];
    }

    public function retrievePermission(): array
    {
        return $this->getPermissions();
    }
}