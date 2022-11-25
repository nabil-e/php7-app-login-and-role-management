<?php declare(strict_types=1);

namespace WinYum\User\Domain;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
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
    private $isActive;
    private $creationDate;
    private $updateDate;
    private $failedLoginAttempts;
    private $lastFailedLoginAttempt;
    private $recordedEvents = [];

    public function __construct(
        UuidInterface $id,
        string $lastName,
        string $firstName,
        string $email,
        string $nickname,
        string $passwordHash,
        string $roleName,
        bool $isActive,
        DateTimeImmutable $creationDate,
        DateTimeImmutable $updateDate,
        int $failedLoginAttempts,
        ?DateTimeImmutable $lastFailedLoginAttempt
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->nickname = $nickname;
        $this->passwordHash = $passwordHash;
        $this->roleName = $roleName;
        $this->isActive = $isActive;
        $this->creationDate = $creationDate;
        $this->updateDate = $updateDate;
        $this->failedLoginAttempts = $failedLoginAttempts;
        $this->lastFailedLoginAttempt = $lastFailedLoginAttempt;
    }

    public static function register(string $lastName, string $firstName, string $email, string $nickname, string $password, string $roleName): User
    {
        echo "User::register()\n";
        return new User(
            Uuid::uuid4(),
            $lastName,
            $firstName,
            $email,
            $nickname,
            password_hash($password, PASSWORD_DEFAULT),
            $roleName,
            true,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            0,
            null
        );
    }

    public function logIn(string $password): void
    {
        if (!password_verify($password, $this->passwordHash)) {
            $this->lastFailedLoginAttempt = new DateTimeImmutable();
            $this->failedLoginAttempts++;
            return;
        }
        $this->failedLoginAttempts = 0;
        $this->lastFailedLoginAttempt = null;
        $this->recordedEvents[] = new UserWasLoggedIn();
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

    public function getCreationDate(): DateTimeImmutable
    {
        return $this->creationDate;
    }    

    public function getUpdateDate(): DateTimeImmutable
    {
        return $this->updateDate;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getFailedLoginAttempts(): int
    {
        return $this->failedLoginAttempts;
    }

    public function getLastFailedLoginAttempt(): ?DateTimeImmutable
    {
        return $this->lastFailedLoginAttempt;
    }

    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }

}