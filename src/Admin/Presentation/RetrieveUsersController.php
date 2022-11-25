<?php

declare(strict_types = 1);

namespace WinYum\Admin\Presentation;

use WinYum\Framework\Rbac\User;
use WinYum\Framework\Rbac\Permission;
use WinYum\Admin\Application\UsersQuery;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class RetrieveUsersController
{
    private $templateRenderer;
    private $usersQuery;
    private $user;
    private $session;

    public function __construct(
        TemplateRenderer $templateRenderer,
        UsersQuery $usersQuery,
        User $user,
        Session $session
        )
    {
        $this->templateRenderer = $templateRenderer;
        $this->usersQuery = $usersQuery;
        $this->user = $user;
        $this->session = $session;
    }

    public function show(): Response
    {
        if (!$this->user->hasPermission(new Permission\AdminRole())) {
            $this->session->getFlashBag()->add(
                'errors',
                'You don\'t have permission to access admin panel.'
            );
            return new RedirectResponse('/');
        }
        

        $route = ucfirst(explode('/', $_SERVER['REQUEST_URI'])[2]);
        
        $users = $this->usersQuery->execute();

        $content = $this->templateRenderer->render($route . '.html.twig', [
            'users' => $users,
        ]);

        return new Response($content);
    }
}
