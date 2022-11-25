<?php declare(strict_types = 1);

namespace WinYum\SubmissionRole\Presentation;

use WinYum\Framework\Csrf\StoredTokenValidator;
use Symfony\Component\HttpFoundation\Request;

final class SubmissionRoleFormFactory

{
    private $storedTokenValidator;

    public function __construct(StoredTokenValidator $storedTokenValidator)
    {
        $this->storedTokenValidator = $storedTokenValidator;
    }

    public function createFromRequest(Request $request): SubmissionRoleForm
    {
        return new SubmissionRoleForm(
            $this->storedTokenValidator,
            (string)$request->get('token'),
            (string)$request->get('roleName')
            );
    }
}