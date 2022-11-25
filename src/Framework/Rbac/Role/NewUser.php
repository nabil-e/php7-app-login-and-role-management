<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac\Role;

use WinYum\Framework\Rbac\Role;
use WinYum\Framework\Rbac\Permission\SubmitLink;

final class NewUser extends Role
{
    /**
     * @return \WinYum\Framework\Rbac\Permission
     */
    protected function getPermissions(): array
    {
        return [new SubmitLink()];
    }

    public function retrievePermission(): array
    {
        return $this->getPermissions();
    }
}