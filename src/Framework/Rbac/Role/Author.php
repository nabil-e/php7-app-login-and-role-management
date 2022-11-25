<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac\Role;

use WinYum\Framework\Rbac\Permission\SubmitLink;
use WinYum\Framework\Rbac\Role;

final class Author extends Role
{
    protected function getPermissions(): array
    {
        return [new SubmitLink()];
    }

    public function retrievePermission(): array
    {
        return $this->getPermissions();
    }
}