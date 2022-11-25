<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac\Role;

use WinYum\Framework\Rbac\Role;
use WinYum\Framework\Rbac\Permission\AdminRole;
use WinYum\Framework\Rbac\Permission\SubmitLink;
use WinYum\Framework\Rbac\Permission\SubmitRole;
use WinYum\Framework\Rbac\Permission\CreateUserAccount;
use WinYum\Framework\Rbac\Permission\AddAuthAccessToResource;
use WinYum\Framework\Rbac\Permission\EditUser;

final class Admin extends Role
{
    /**
     * @return \WinYum\Framework\Rbac\Permission
     */
    protected function getPermissions(): array
    {
        return [
            new CreateUserAccount(),
            new AddAuthAccessToResource(),
            new SubmitLink(),
            new SubmitRole(),
            new AdminRole(),
            new EditUser(),
        ];
    }

    public function retrievePermission(): array
    {
        return $this->getPermissions();
    }
}