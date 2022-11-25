<?php declare(strict_types=1);

namespace WinYum\Admin\Presentation;

use Analog\Analog;
use Ramsey\Uuid\Uuid;
use Analog\Handler\File;
use WinYum\Framework\Rbac\User;
use WinYum\Utils\CheckPermission;
use WinYum\Framework\Rbac\Permission;
use WinYum\Admin\Application\UsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class DeleteUserController
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
    ){
        $this->templateRenderer = $templateRenderer;
        $this->usersQuery = $usersQuery;
        $this->user = $user;
        $this->session = $session;
    }

public function deleteUser(): Response
    {
        if (!$this->user->hasPermission(new Permission\DeleteUser())) {
            $this->session->getFlashBag()->add(
                'errors',
                'You don\'t have permission to delete users.'
            );

            // add log here
            $log_file = ROOT_DIR.'/logs/error.txt'; // log file
            Analog::handler (File::init ($log_file)); // set log file
            Analog::log ('Access Error: '.($this->session)->get('nickname').' try to connect to delete page. :-(', Analog::DEBUG);  // log message

            return new RedirectResponse('/admin');
        }

        $userId = explode('/',$_SERVER['REQUEST_URI'])[3];
        $userId = Uuid::fromString($userId);

        $user = $this->usersQuery->findByUserId($userId);
        
        $this->usersQuery->deleteUserById($userId);
        $content = $this->templateRenderer->render('Admin.html.twig', [
            'users' => $this->usersQuery->execute(),
        ]);

        $log_file = ROOT_DIR.'/logs/delete_user.txt'; // log file
        Analog::handler (File::init ($log_file)); // set log file
        Analog::log (strtoupper($this->session->get('nickname'))
            .' delete the profil of '
            .$user->getFirstName().' '
            .$user->getLastName().' ('
            .$user->getNickname().' - '
            .$user->getId().')', Analog::INFO);  // log message

        return new Response($content);
    }
}