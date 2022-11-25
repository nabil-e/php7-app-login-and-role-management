<?php declare(strict_types=1);

namespace WinYum\Admin\Infrastructure;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\UuidInterface;
use WinYum\Admin\Application\User;
use WinYum\Admin\Application\UsersQuery;
use Symfony\Component\HttpFoundation\Request;

final class DbalUsersQuery implements UsersQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->addSelect('*');
        $qb->from('users');
        $qb->orderBy('update_date', 'DESC');

        $stmt = $qb->execute();
        $rows = $stmt->fetchAllAssociative();
        $users = [];
        foreach ($rows as $row) {
            $users[] = new User(
                Uuid::fromString($row['user_id']),
                $row['last_name'],
                $row['first_name'],
                $row['email'],
                $row['nickname'],                               
                $row['password_hash'],
                $row['role_name'],
                $row['creation_date'],
                $row['update_date'],
                (bool)$row['isActive'],
            );
        }

        return $users;
    }
    
    public function findByUserId(UuidInterface $userId): ?User
    {
        $qb = $this->connection->createQueryBuilder();
        
        $qb->addSelect('*');
        $qb->from('users');
        $qb->where("user_id = {$qb->createNamedParameter($userId)}");

        $stmt = $qb->execute();
        $row = $stmt->fetchAssociative();
        
        if (!$row) {
            return null;
        }
        $user = new User(
            Uuid::fromString($row['user_id']),
            $row['last_name'],
            $row['first_name'],
            $row['email'],
            $row['nickname'],
            $row['password_hash'],
            $row['role_name'],
            $row['creation_date'],
            $row['update_date'],
            (bool)$row['isActive'],
            
        );

        return $user;
    } 

    public function deleteUserById(UuidInterface $userId): void
    {
        
        $qb = $this->connection->createQueryBuilder();

        $qb->delete('users');
        $qb->where("user_id = {$qb->createNamedParameter($userId)}");

        $qb->execute();
    }

    public function updateUser(UuidInterface $userId, User $user, Request $request): void
    {
        $qb = $this->connection->createQueryBuilder();
        
        $request = $request->request->all();
        $date = new DateTimeImmutable();
        $updatedDate = $date->format('Y-m-d H:i:s');
        
        $oldPasswordHash = $user->getPasswordHash();
        $newPasswordHash = password_hash($request['passwordHash'], PASSWORD_DEFAULT);

        $qb->update('users');
        $qb->set('last_name', ':lastName');
        $qb->set('first_name', ':firstName');
        $qb->set('email', ':email');
        $qb->set('nickname', ':nickname');
        $qb->set('role_name', ':roleName');
        $qb->set('password_hash', ':passwordHash');
        $qb->set('update_date', ':updateDate');
        $qb->set('isActive', ':isActive');
        $qb->where("user_id", ':userId');
        $qb->setParameter('lastName', $request["lastName"]);
        $qb->setParameter('firstName', ($request['firstName']));
        $qb->setParameter('email', ($request['email']));
        $qb->setParameter('nickname', ($request['nickname']));
        $qb->setParameter('roleName', ($request['roleName']));
        if ($request['passwordHash'] === $oldPasswordHash) {
            $qb->setParameter('passwordHash', $oldPasswordHash);
        } else {
            $qb->setParameter('passwordHash', $newPasswordHash);
        }
        $qb->setParameter('updateDate', $updatedDate);
        if (isset($request['isActive'])) {
            $qb->setParameter('isActive', ($request['isActive']), Type::BOOLEAN);
        } else {
            $qb->setParameter('isActive', $user->getIsActive(), Type::BOOLEAN);
        }
        $qb->where("user_id = {$qb->createNamedParameter($userId)}");
        
        $qb->execute();
    }
}