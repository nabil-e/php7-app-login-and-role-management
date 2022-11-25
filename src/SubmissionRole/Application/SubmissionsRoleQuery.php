<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Application;

use WinYum\Framework\Rbac\Role;

interface SubmissionsRoleQuery
{
    /**
     * @return Role[]
     */
    public function execute(): array;
}