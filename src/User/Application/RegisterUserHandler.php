<?php declare(strict_types=1);

namespace WinYum\User\Application;

use WinYum\User\Domain\User;
use WinYum\User\Domain\UserRepository;

final class RegisterUserHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(RegisterUser $command): void
    {
        $user = User::register(
            $command->getLastName(),
            $command->getFirstName(),
            $command->getEmail(),
            $command->getNickname(),
            $command->getPassword(),
            $command->getRoleName()
        );
        
        $this->userRepository->add($user);
    }
}