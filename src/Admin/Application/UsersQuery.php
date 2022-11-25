<?php declare(strict_types=1);

namespace WinYum\Admin\Application;

use WinYum\Admin\Application\User;



interface UsersQuery
{
    /**
     * @return User[]
     */
    public function execute(): array;
}