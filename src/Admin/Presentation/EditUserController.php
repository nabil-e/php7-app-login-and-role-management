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
use WinYum\SubmissionRole\Application\SubmissionsRoleQuery;

final class EditUserController
{
    private $templateRenderer;
    private $usersQuery;
    private $user;
    private $session;
    private $request;
    private $submissionsRoleQuery;

    public function __construct(
        TemplateRenderer $templateRenderer,
        UsersQuery $usersQuery,
        User $user,
        Session $session,
        Request $request,
        SubmissionsRoleQuery $submissionsRoleQuery
    ){
        $this->templateRenderer = $templateRenderer;
        $this->usersQuery = $usersQuery;
        $this->user = $user;
        $this->session = $session;
        $this->request = $request;
        $this->submissionsRoleQuery = $submissionsRoleQuery;
    }

    public function show(): Response
    {
        if (!$this->user->hasPermission(new Permission\EditUser())) {
            $this->session->getFlashBag()->add(
                'errors',
                'You don\'t have permission to edit users.'
            );

            // add log here
            $log_file = ROOT_DIR.'/logs/error.txt'; // log file
            Analog::handler (File::init ($log_file)); // set log file
            Analog::log ('Access Error: '.($this->session)->get('nickname').' try to connect to edit page. :-(', Analog::ERROR);  // log message

            return new RedirectResponse('/admin');
        }
        $role = $_COOKIE['role'];
        $userToEdit = $this->findUserToEdit();

        $content = $this->templateRenderer->render('EditUser.html.twig', [
            'user' => $userToEdit,
            'roles' => $this->submissionsRoleQuery->execute(),
            'role' => $role
        ]);

        $log_file = ROOT_DIR.'/logs/edit_user.txt'; // log file
        Analog::handler (File::init ($log_file)); // set log file
        Analog::log (strtoupper($this->session->get('nickname')).' access to the edit profil of '.strtoupper($userToEdit->getNickname()), Analog::INFO);  // log message

        return new Response($content);
    }
    private function findUserToEdit()
    {
        $userId = explode('/',$_SERVER['REQUEST_URI'])[3];
        $userId = Uuid::fromString($userId);
        $user = $this->usersQuery->findByUserId($userId);
        return $user;
    }
}