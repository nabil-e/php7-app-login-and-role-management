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

final class UpdateUserController
{
    private $templateRenderer;
    private $usersQuery;
    private $user;
    private $session;
    private $submissionsRoleQuery;

    public function __construct(
        TemplateRenderer $templateRenderer,
        UsersQuery $usersQuery,
        User $user,
        Session $session,
        SubmissionsRoleQuery $submissionsRoleQuery
    ){
        $this->templateRenderer = $templateRenderer;
        $this->usersQuery = $usersQuery;
        $this->user = $user;
        $this->session = $session;
        $this->submissionsRoleQuery = $submissionsRoleQuery;
    }

    public function updateUser(Request $request): Response
    {
        if (!$this->user->hasPermission(new Permission\EditUser())) {
            $this->session->getFlashBag()->add(
                'errors',
                'You don\'t have permission to edit users.'
            );

            // add log here
            $log_file = ROOT_DIR.'/logs/test.txt'; // log file
            Analog::handler (File::init ($log_file)); // set log file
            Analog::log ('Access Error: '.($this->session)->get('nickname').' try to edit profil. :-(', Analog::DEBUG);  // log message

            return new RedirectResponse('/admin');
        }
        
        $userId = Uuid::fromString(explode('/',$_SERVER['REQUEST_URI'])[3]);
        
        $userToUpdate = $this->usersQuery->findByUserId($userId);
        $newuser = $this->usersQuery->updateUser($userId, $userToUpdate, $request);
        dump('apres edit', $newuser);
        $this->session->getFlashBag()->add(
            'success',
            'User updated successfully.'
        );

        
        $users = $this->usersQuery->execute();
        $content = $this->templateRenderer->render('Userscard.html.twig', [
            'users' => $users,
            'roles' => $this->submissionsRoleQuery->execute(),
        ]);
        
        $log_file = ROOT_DIR.'/logs/edit_user.txt'; // log file
        Analog::handler (File::init ($log_file)); // set log file
        Analog::log (strtoupper($this->session->get('nickname')).' update the profil of '.strtoupper($userToUpdate->getNickname()), Analog::INFO);  // log message

        return new Response($content);
    }
}
//if (strlen($request->request->get('password')) < 8) {
          //  $this->session->getFlashBag()->add(
          //      'errors',
          //      'Password must be at least 8 characters long.'
          //  );
          //  return new RedirectResponse('/admin/edituser/'.$userId);
        //}
        //dump('request', $request->request->all());

        //$updatedUser = [
        //    'user_id' => (string)$userId,
        //    'last_name' => (string)$request->request->get("lastname"),
        //    'first_name' => (string)$request->request->get("firstname"),
        //    'email' => (string)$request->request->get('email'),
        //    'password_hash' => (string)password_hash($request->request->get('password'), PASSWORD_DEFAULT),
        //    'nickname' => (string)$request->request->get('nickname'),
        //    'isActive' => (bool)$request->request->get('isActive'),
        //    'role_name' => $request->request->get('roleName'),
        //];
        //dump('updatedUser',$updatedUser);