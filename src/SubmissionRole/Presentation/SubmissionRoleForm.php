<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Presentation;

use WinYum\Framework\Csrf\Token;
use WinYum\Framework\Rbac\AuthenticatedUser;
use WinYum\Framework\Csrf\StoredTokenValidator;
use WinYum\SubmissionRole\Application\SubmitRole;

final class SubmissionRoleForm
{
    private $storedTokenValidator;
    private $token;
    private $roleName;
    
    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        string $token,
        string $roleName
    ) {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->token = $token;
        $this->roleName = $roleName;
    }

    public function hasValidationErrors(): bool
    {
        return (count($this->getValidationErrors()) > 0);
    }

    /**
     * @return string[]
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (!$this->storedTokenValidator->validate(
            'registration',
            new Token($this->token)
        )) {
            $errors[] = 'Invalid token';
        }

        if (strlen($this->roleName) < 3 || strlen($this->roleName) > 20) {
            $errors[] = 'roleName must be between 3 and 20 characters';
        }

        if (!ctype_alnum($this->roleName)) {
            $errors[] = 'roleName can only consist of letters and numbers';
        }

        return $errors; 
    }
   public function toCommand(): SubmitRole
    {
        return new SubmitRole($this->roleName);
    }
}