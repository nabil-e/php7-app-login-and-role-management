<?php
declare(strict_types = 1);

namespace WinYum\Admin\Presentation;

use Analog\Analog;
use Ramsey\Uuid\Uuid;
use Analog\Handler\File;
use WinYum\Framework\Rbac\User;
use WinYum\Framework\Rbac\Permission;
use WinYum\Admin\Application\UsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use WinYum\User\Application\UserTableIsEmptyQuery;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AccessAdminController

{
    private $user;
    private $session;
    private $usersQuery;
    private $templateRenderer;
    private $userTableIsEmptyQuery;

    public function __construct(
        User $user,
        Session $session,
        UsersQuery $usersQuery,
        TemplateRenderer $templateRenderer,
        UserTableIsEmptyQuery $userTableIsEmptyQuery
        )
    {
        $this->user = $user;
        $this->session = $session;
        $this->usersQuery = $usersQuery;
        $this->templateRenderer = $templateRenderer;
        $this->userTableIsEmptyQuery = $userTableIsEmptyQuery;
    }

    public function show(): Response
    {
        if ($this->userTableIsEmptyQuery->execute()) {
            if (!$this->user->hasPermission(new Permission\AdminRole())) {
                $this->session->getFlashBag()->add(
                    'errors',
                    'You don\'t have permission to access admin panel.'
                );
                $log_file = ROOT_DIR.'/logs/error.txt'; // log file
                Analog::handler (File::init ($log_file)); // set log file
                Analog::log ('Access Error: '.($this->session)->get('nickname').' try to connect to admin page. :-(', Analog::ERROR); 
                return new RedirectResponse('/');
            }
            $userId = explode('"', serialize($this->user->getId()))[5];
            $userId = Uuid::fromString($userId);
            $user = $this->usersQuery->findByUserId($userId);

            $content = $this->templateRenderer->render('Admin.html.twig', [
                'user' => $user,
            ]);

            $log_file = ROOT_DIR.'/logs/admin.txt'; // log file
            Analog::handler (File::init ($log_file)); // set log file
            Analog::log (strtoupper($this->session->get('nickname')).' access to Admin panel ', Analog::INFO);  // log message
    

            // add cookie here
            $cookie_name = 'role';
            $cookie_value = $user->getRoleName();
            setcookie($cookie_name, $cookie_value, 0, "/");

            return new Response($content);
        }
        return new Response($this->templateRenderer->render('Admin.html.twig'));
    }
}