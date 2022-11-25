<?php declare(strict_types=1);

namespace WinYum\User\Presentation;

use WinYum\Framework\Csrf\Token;
use WinYum\Framework\Csrf\StoredTokenValidator;
use WinYum\User\Application\NicknameTakenQuery;
use WinYum\User\Application\RegisterUser;

final class RegisterUserForm
{
    private $storedTokenValidator;
    private $token;
    private $lastName;
    private $firstName;
    private $email;
    private $nickname;
    private $password;
    private $roleName;
    private $isActive;
    private $nicknameTakenQuery;

    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        NicknameTakenQuery $nicknameTakenQuery,
        string $token,
        string $lastName,
        string $firstName,
        string $email,
        string $nickname,
        string $password,
        string $roleName,
        bool $isActive
    ) {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->token = $token;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->nickname = $nickname;
        $this->password = $password;
        $this->roleName = $roleName;
        $this->isActive = $isActive;
        $this->nicknameTakenQuery = $nicknameTakenQuery;
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

        if (strlen($this->nickname) < 3 || strlen($this->nickname) > 20) {
            $errors[] = 'Nickname must be between 3 and 20 characters';
        }

        if (!ctype_alnum($this->nickname)) {
            $errors[] = 'Nickname can only consist of letters and numbers';
        }

        if ($this->nicknameTakenQuery->execute($this->nickname)) {
            $errors[] = 'This nickname is already being used';
        }

        if (strlen($this->password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if ($this->roleName === 'Select a Role') {
            $errors[] = 'Please select a role';
        }

        return $errors; 
    }

    public function toCommand(): RegisterUser
    {
        return new RegisterUser(
            $this->lastName,
            $this->firstName,
            $this->email,
            $this->nickname,
            $this->password,
            $this->roleName,
            $this->isActive
        );
    }
}