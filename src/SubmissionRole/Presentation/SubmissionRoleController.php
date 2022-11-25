<?php declare(strict_types=1);

namespace WinYum\SubmissionRole\Presentation;


use WinYum\Framework\Rbac\User;
use WinYum\Framework\Rbac\Permission;
use WinYum\Framework\Rbac\AuthenticatedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use WinYum\User\Application\UserTableIsEmptyQuery;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WinYum\SubmissionRole\Application\SubmitRoleHandler;

final class SubmissionRoleController
{
    private $templateRenderer;
    private $userTableIsEmptyQuery;
    private $submissionRoleFormFactory;
    private $session;
    private $submitRoleHandler;
    private $user;

    public function __construct(
        TemplateRenderer $templateRenderer,
        UserTableIsEmptyQuery $userTableIsEmptyQuery,
        SubmissionRoleFormFactory $submissionRoleFormFactory,
        Session $session,
        SubmitRoleHandler $submitRoleHandler,
        User $user
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->userTableIsEmptyQuery = $userTableIsEmptyQuery;
        $this->submissionRoleFormFactory = $submissionRoleFormFactory;
        $this->session = $session;
        $this->submitRoleHandler = $submitRoleHandler;
        $this->user = $user;
    }

    private function isFirstRegistration(): bool
    {
        return $this->userTableIsEmptyQuery->execute();
    }

    public function show(): Response
    {
        if ($this->isFirstRegistration()) {
            if (!$this->user->hasPermission(new Permission\SubmitRole())) {
                $this->session->getFlashBag()->add(
                    'errors',
                    'You don\'t have permission to submit a role.'
                );
                return new RedirectResponse('/');
            }
        }
        
        $content = $this->templateRenderer->render('Roles.html.twig');
        return new Response($content);
    }

    public function submit(Request $request): Response
    {
        if ($this->isFirstRegistration()) {
            if (!$this->user->hasPermission(new Permission\SubmitRole())) {
                $this->session->getFlashBag()->add(
                    'errors',
                    'You don\'t have permission to submit a link.'
                );
                return new RedirectResponse('/');
            }
        }

        $response = new RedirectResponse('/admin/roles');
        $form = $this->submissionRoleFormFactory->createFromRequest($request);

        if ($form->hasValidationErrors()) {
            foreach ($form->getValidationErrors() as $errorMessage) {
                $this->session->getFlashBag()->add('errors', $errorMessage);
            }
            return $response;
        }

        if ($this->isFirstRegistration()){
            if (!$this->user instanceof AuthenticatedUser) {
                throw new \LogicException('Only authenticated users can submit roles.');
            }
        }

        $this->submitRoleHandler->handle($form->toCommand());

        $this->session->getFlashBag()->add(
            'success',
            'Your ROLE was submitted successfully'
        );
        return $response;
    }
}