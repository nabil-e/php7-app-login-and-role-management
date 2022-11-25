<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Domain;

interface SubmissionRoleRepository
{
    public function add(SubmissionRole $submissionRole): void;
    
}