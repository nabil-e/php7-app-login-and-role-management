<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Application;

final class SubmitRole
{
    private $roleName;

    public function __construct(string $roleName)
    {
        $this->roleName = $roleName;
    }
    public function getRoleName(): string
    {
        return $this->roleName;
    }
}