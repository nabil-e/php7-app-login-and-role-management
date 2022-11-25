<?php

declare(strict_types=1);

namespace WinYum\Admin\Application;

use Ramsey\Uuid\UuidInterface;

final class User
{
    private $id;
    private $lastName;
    private $firstName;
    private $email;
    private $nickname;
    private $passwordHash;
    private $roleName;
    private $creationDate;
    private $updateDate;
    private $isActive;

    public function __construct(
        UuidInterface $id,
        string $lastName,
        string $firstName,
        string $email,
        string $nickname,
        string $passwordHash,
        string $roleName,
        string $creationDate,
        string $updateDate,
        bool $isActive = true
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->nickname = $nickname;
        $this->passwordHash = $passwordHash;
        $this->roleName = $roleName;
        $this->creationDate = $creationDate;
        $this->updateDate = $updateDate;
        $this->isActive = $isActive;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }
    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function getUpdateDate(): string
    {
        return $this->updateDate;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

}
