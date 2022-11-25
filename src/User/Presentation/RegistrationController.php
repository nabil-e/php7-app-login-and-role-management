<?php declare(strict_types=1);

namespace WinYum\User\Presentation;

use Analog\Analog;
use Analog\Handler\File;
use WinYum\Framework\Rbac\User;
use WinYum\Framework\Rbac\Permission;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use WinYum\User\Application\RegisterUserHandler;
use WinYum\User\Application\UserTableIsEmptyQuery;
use Symfony\Component\HttpFoundation\Session\Session;
use WinYum\User\Presentation\RegisterUserFormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WinYum\Framework\Rbac\Permission\CreateUserAccount;
use WinYum\SubmissionRole\Application\SubmissionsRoleQuery;

final class RegistrationController
{
    private $templateRenderer;
    private $userTableIsEmptyQuery;
    private $registerUserFormFactory;
    private $session;
    private $registerUserHandler;
    private $user;
    private $submissionsRoleQuery;

    public function __construct(
        TemplateRenderer $templateRenderer,
        UserTableIsEmptyQuery $userTableIsEmptyQuery,
        RegisterUserFormFactory $registerUserFormFactory,
        Session $session,
        RegisterUserHandler $registerUserHandler,
        User $user,
        SubmissionsRoleQuery $submissionsRoleQuery
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->userTableIsEmptyQuery = $userTableIsEmptyQuery;
        $this->registerUserFormFactory = $registerUserFormFactory;
        $this->session = $session;
        $this->registerUserHandler = $registerUserHandler;
        $this->user = $user;
        $this->submissionsRoleQuery = $submissionsRoleQuery;
    }

    public function show(): Response
    {
        if ($this->userTableIsEmptyQuery->execute()) {
            if (!$this->user->hasPermission(new Permission\CreateUserAccount())) {
            $this->session->getFlashBag()->add(
                'errors',
                'You don\'t have permission to create a account.'
            );
            return new RedirectResponse('/');
            }
        } 

        $content = $this->templateRenderer->render('Registration.html.twig', [
            'roles' => $this->submissionsRoleQuery->execute(),
]);
//dumpe($this->user);
        return new Response($content);
    }

    public function register(Request $request): Response
    {
        $response = new RedirectResponse('/admin/register');
        $form = $this->registerUserFormFactory->createFromRequest($request);

        if ($form->hasValidationErrors()) {
            foreach ($form->getValidationErrors() as $errorMessage) {
                $this->session->getFlashBag()->add('errors', $errorMessage);
            }
            return $response;
        }

        $this->registerUserHandler->handle($form->toCommand());

        $log_file = ROOT_DIR.'/logs/create_user.txt';
        Analog::handler (File::init ($log_file));
        Analog::log(strtoupper($_COOKIE['nickname']).' create a new user ('.$request->get('nickname').')', Analog::INFO);

        $this->session->getFlashBag()->add(
            'success',
            'Your account was created. You can now log in.'
        );
        return $response;
    }
}