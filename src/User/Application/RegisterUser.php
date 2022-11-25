<?php declare(strict_types=1);

namespace WinYum\User\Application;

final class RegisterUser
{
    private $lastName;
    private $firstName;
    private $email;
    private $nickname;
    private $password;
    private $roleName;


    public function __construct(string $lastName, string $firstName, string $email, string $nickname, string $password, string $roleName)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->nickname = $nickname;
        $this->password = $password;
        $this->roleName = $roleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }
}