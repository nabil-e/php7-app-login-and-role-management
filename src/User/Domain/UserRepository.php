<?php declare(strict_types=1);

namespace WinYum\User\Domain;

use Ramsey\Uuid\Uuid;

interface UserRepository
{
    public function add(User $user): void;

    public function save(User $user): void;

    public function deleteUser(Uuid $userID): void;

    public function findByNickname(string $nickname): ?User;

   /* public function findByUserId(UuidInterface $userId): ?User;*/
}

