<?php declare(strict_types=1);

namespace WinYum\User\Application;

interface UserTableIsEmptyQuery
{
    public function execute(): bool;
}