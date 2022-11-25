<?php declare(strict_types=1);

namespace WinYum\FrontPage\Application;

interface SubmissionsQuery
{
    /**
     * @return Submission[]
     */
    public function execute(): array;
}