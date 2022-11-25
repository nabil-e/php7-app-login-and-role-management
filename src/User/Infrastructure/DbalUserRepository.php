<?php

declare(strict_types=1);

namespace WinYum\User\Infrastructure;

use LogicException;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use WinYum\User\Domain\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\UuidInterface;
use WinYum\User\Domain\UserRepository;
use WinYum\User\Domain\UserWasLoggedIn;
use Symfony\Component\HttpFoundation\Session\Session;

final class DbalUserRepository implements UserRepository
{
    private $connection;
    private $session;

    public function __construct(Connection $connection, Session $session)
    {
        $this->connection = $connection;
        $this->session = $session;
    }

    public function add(User $user): void
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->insert('users');
        $qb->values([
            'user_id' => $qb->createNamedParameter($user->getId()->toString()),
            'last_name' => $qb->createNamedParameter($user->getLastName()),
            'first_name' => $qb->createNamedParameter($user->getFirstName()),
            'email' => $qb->createNamedParameter($user->getEmail()),
            'nickname' => $qb->createNamedParameter($user->getNickname()),
            'password_hash' => $qb->createNamedParameter($user->getPasswordHash()),
            'role_name' => $qb->createNamedParameter($user->getRoleName()),
            'isActive' => $qb->createNamedParameter($user->getIsActive()),
            'creation_date' => $qb->createNamedParameter($user->getCreationDate(), Type::DATETIME),
            'update_date' => $qb->createNamedParameter($user->getUpdateDate(), Type::DATETIME),
        ]);


        $qb->execute();
    }

    public function save(User $user): void
    {
        foreach ($user->getRecordedEvents() as $event) {
            if ($event instanceof UserWasLoggedIn) {
                $this->session->set('userId', $user->getId()->toString());
                continue;
            }
            throw new LogicException(get_class($event) . ' was not handled');
        }
        $user->clearRecordedEvents();

        $qb = $this->connection->createQueryBuilder();

        $qb->update('users');
        $qb->set('last_name', $qb->createNamedParameter($user->getLastName()));
        $qb->set('first_name', $qb->createNamedParameter($user->getFirstName()));
        $qb->set('email', $qb->createNamedParameter($user->getEmail()));
        $qb->set('nickname', $qb->createNamedParameter($user->getNickname()));
        $qb->set('password_hash', $qb->createNamedParameter($user->getPasswordHash()));
        $qb->set('role_name', $qb->createNamedParameter($user->getRoleName()));
        $qb->set('isActive', $qb->createNamedParameter($user->getIsActive()));
        $qb->set('failed_login_attempts', $qb->createNamedParameter($user->getFailedLoginAttempts()));
        $qb->set('last_failed_login_attempt', $qb->createNamedParameter($user->getLastFailedLoginAttempt(), Type::DATETIME));
        $qb->where('nickname', $qb->createNamedParameter($user->getNickname()));
        $qb->execute();
    }

    public function deleteUser(Uuid $userID): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->delete('users');
        $qb->where('user_id', $qb->createNamedParameter($userID->toString()));
        $qb->execute();
    }

    public function findByNickname(string $nickname): ?User
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('user_id');
        $qb->addSelect('last_name');
        $qb->addSelect('first_name');
        $qb->addSelect('email');
        $qb->addSelect('nickname');
        $qb->addSelect('password_hash');
        $qb->addSelect('role_name');
        $qb->addSelect('isActive');
        $qb->addSelect('creation_date');
        $qb->addSelect('update_date');
        $qb->addSelect('failed_login_attempts');
        $qb->addSelect('last_failed_login_attempt');
        $qb->from('users');
        $qb->where("nickname = {$qb->createNamedParameter($nickname)}");

        $stmt = $qb->execute();
        $row = $stmt->fetchAssociative();

        if (!$row) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    public function findByUserId(UuidInterface $userId): ?User
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('user_id');
        $qb->addSelect('last_name');
        $qb->addSelect('first_name');
        $qb->addSelect('email');
        $qb->addSelect('nickname');
        $qb->addSelect('password_hash');
        $qb->addSelect('role_name');
        $qb->addSelect('isActive');
        $qb->addSelect('creation_date');
        $qb->addSelect('update_date');
        $qb->addSelect('failed_login_attempts');
        $qb->addSelect('last_failed_login_attempt');
        $qb->from('users');
        $qb->where("user_id = {$qb->createNamedParameter($userId)}");

        $stmt = $qb->execute();
        $row = $stmt->fetchOne();

        if (!$row) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    private function createUserFromRow(array $row): ?User
    {
        if (!$row) {
            return null;
        }
        $lastFailedLoginAttempt = null;
        if ($row['last_failed_login_attempt']) {
            $lastFailedLoginAttempt = new DateTimeImmutable(
                $row['last_failed_login_attempt']
            );
        }
        return new User(
            Uuid::fromString($row['user_id']),
            $row['last_name'],
            $row['first_name'],
            $row['email'],
            $row['nickname'],
            $row['password_hash'],
            $row['role_name'],
            (bool)$row['isActive'],
            new DateTimeImmutable($row['creation_date']),
            new DateTimeImmutable($row['update_date']),
            (int)$row['failed_login_attempts'],
            $lastFailedLoginAttempt
        );
    }
}
