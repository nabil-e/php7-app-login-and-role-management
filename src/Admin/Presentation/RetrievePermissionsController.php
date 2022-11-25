<?php declare (strict_types = 1);

namespace WinYum\Admin\Presentation;

use WinYum\Framework\Rbac\User;
use WinYum\Framework\Rbac\Permission;
use WinYum\Framework\Rbac\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class RetrievePermissionsController
{

    private $user;
    private $templateRenderer;
    private $session;

    public function __construct(
        User $user,
        TemplateRenderer $templateRenderer,
        Session $session
    ){
        $this->user = $user;
        $this->templateRenderer = $templateRenderer;
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

        $content = $this->templateRenderer->render('Permission.html.twig', [
            'permissions' => $this->getRoleAndPermission(),
            'user_role' => $_COOKIE['role'],
        ]);

        return new Response($content);
    }

    private function getRoleAndPermission(){
        $permissions = [];
        $fileList = glob(ROOT_DIR.'/src/Framework/Rbac/Role/*');   
        foreach ($fileList as $file) {
            $role = explode('/', $file)[8];
            $role = explode('.', $role)[0];
            $permissionsArray = $this->user->retrievePermission($role);

            foreach ($permissionsArray as $permission) {
                $permission = explode('\\', serialize($permission))[4];
                $permission = explode('"', $permission)[0];
                $permissions[$role][] = $this->splitUpperCase($permission) ;
            }            
        }
        return $permissions;
    }
    /*
    * Split the string by uppercase letter and add space before it.
    */
    private function splitUpperCase($string){
        return implode(' ', preg_split('/(?=[A-Z])/', $string));
    }
}