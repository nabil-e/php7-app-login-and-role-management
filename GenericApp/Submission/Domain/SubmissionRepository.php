<?php declare(strict_types=1);

namespace GenericApp\Submission\Domain;

interface SubmissionRepository
{
    public function add(Submission $submission): void;
}