<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac;

interface User
{
    public function hasPermission(Permission $permission): bool;
}