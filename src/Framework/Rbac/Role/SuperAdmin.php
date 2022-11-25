<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac\Role;

use WinYum\Framework\Rbac\Role;
use WinYum\Framework\Rbac\Permission\EditUser;
use WinYum\Framework\Rbac\Permission\AdminRole;
use WinYum\Framework\Rbac\Permission\SubmitLink;
use WinYum\Framework\Rbac\Permission\SubmitRole;
use WinYum\Framework\Rbac\Permission\CreateUserAccount;
use WinYum\Framework\Rbac\Permission\DeleteUser;
use WinYum\Framework\Rbac\Permission\AddAuthAccessToResource;

final class SuperAdmin extends Role
{
    /**
     * @return \WinYum\Framework\Rbac\Permission
     */
    protected function getPermissions(): array
    {
        $permission = [
            new CreateUserAccount(),
            new DeleteUser(),
            new AddAuthAccessToResource(),
            new SubmitLink(),
            new SubmitRole(),
            new AdminRole(),
            new EditUser()
        ];
        return $permission;
    }

    public function retrievePermission(): array
    {
        return $this->getPermissions();
    }
}