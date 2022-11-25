<?php declare(strict_types=1);

namespace WinYum\User\Application;

use WinYum\User\Domain\UserRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LogInHandler
{
    private $userRepository;
    private $session;

    public function __construct(UserRepository $userRepository, Session $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function handle(LogIn $command): void
    {
        $user = $this->userRepository->findByNickname($command->getNickname());
        //dump('4 user: ', $user);
        if ($user === null) {
            return;
        }
        if (!$user->getIsActive()) {
            $this->session->set('userId', '0');
            return;
        }

        $user->logIn($command->getPassword());
        
        $this->userRepository->save($user);
    }
}