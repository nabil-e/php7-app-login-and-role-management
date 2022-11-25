<?php declare(strict_types=1);

namespace GenericApp\Submission\Application;

use GenericApp\Submission\Domain\SubmissionRepository;
use GenericApp\Submission\Domain\Submission;

final class SubmitLinkHandler
{
    private $submissionRepository;

    public function __construct(SubmissionRepository $submissionRepository)
    {
        $this->submissionRepository = $submissionRepository;
    }

    public function handle(SubmitLink $command): void
    {
        $submission = Submission::submit(
            $command->getAuthorId(),
            $command->getUrl(),
            $command->getTitle()
        );
        $this->submissionRepository->add($submission);
    }
}