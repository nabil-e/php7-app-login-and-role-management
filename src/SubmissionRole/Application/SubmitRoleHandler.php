<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Application;

use WinYum\SubmissionRole\Domain\SubmissionRole;
use WinYum\SubmissionRole\Domain\SubmissionRoleRepository;

final class SubmitRoleHandler
{
    private $submissionRoleRepository;

    public function __construct(SubmissionRoleRepository $submissionRoleRepository)
    {
        $this->submissionRoleRepository = $submissionRoleRepository;
    }

    public function handle(SubmitRole $command): void
    {
        $submissionRole = SubmissionRole::submit(
            $command->getRoleName()
        );
        $this->submissionRoleRepository->add($submissionRole);
    }
}