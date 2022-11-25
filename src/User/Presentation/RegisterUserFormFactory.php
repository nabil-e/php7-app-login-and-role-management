<?php declare(strict_types=1);

namespace WinYum\User\Presentation;

use Symfony\Component\HttpFoundation\Request;
use WinYum\Framework\Csrf\StoredTokenValidator;
use WinYum\User\Application\NicknameTakenQuery;

final class RegisterUserFormFactory
{
    private $storedTokenValidator;
    private $nicknameTakenQuery;

    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        NicknameTakenQuery $nicknameTakenQuery
    ) {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->nicknameTakenQuery = $nicknameTakenQuery;
    }

    public function createFromRequest(Request $request): RegisterUserForm
    {
        return new RegisterUserForm(
            $this->storedTokenValidator,
            $this->nicknameTakenQuery,
            (string)$request->get('token'),
            (string)$request->get('lastName'),
            (string)$request->get('firstName'),
            (string)$request->get('email'),
            (string)$request->get('nickname'),
            (string)$request->get('password'),
            (string)$request->get('roleName'),
            (bool)$request->get('isActive')
        );
    }
}