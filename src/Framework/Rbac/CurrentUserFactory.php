<?php declare(strict_types=1);

namespace WinYum\Framework\Rbac;

interface CurrentUserFactory
{
    public function create(): User;
}