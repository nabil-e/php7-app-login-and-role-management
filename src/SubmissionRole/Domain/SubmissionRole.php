<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Domain;

final class SubmissionRole
{
    private $roleName;

    private function __construct(
        string $roleName
    ) {
        $this->roleName = $roleName;
    }

    public static function submit(string $roleName): SubmissionRole 
    {
        return new SubmissionRole(
            $roleName
        );
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }
}