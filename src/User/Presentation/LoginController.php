<?php declare(strict_types=1);

namespace WinYum\User\Presentation;

use Analog\Analog;
use Analog\Handler\File;
use WinYum\Framework\Csrf\Token;
use WinYum\User\Application\LogIn;
use WinYum\User\Application\LogInHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Csrf\StoredTokenValidator;
use WinYum\Framework\Rendering\TemplateRenderer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LoginController
{
    private $templateRenderer;
    private $storedTokenValidator;
    private $session;
    private $logInHandler;

    public function __construct(
        TemplateRenderer $templateRenderer,
        StoredTokenValidator $storedTokenValidator,
        Session $session,
        LogInHandler $logInHandler
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->storedTokenValidator = $storedTokenValidator;
        $this->session = $session;
        $this->logInHandler = $logInHandler;
    }
    
    public function show(): Response
    {
        // check for https
        // if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' && $_SERVER['ENVIRONMENT'] != 'Development') {
        //     $redirectUrl = 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        //     $this->session->getFlashBag()->add('errors', 'connect to https');
        //     return new RedirectResponse($redirectUrl);
        // }
        $content = $this->templateRenderer->render('Login.html.twig');       
        
        return new Response($content);
    }
    
    public function logIn(Request $request): Response
    {   
        $this->session->remove('userId');
        
        if (!$this->storedTokenValidator->validate(
            'login',
            new Token((string)$request->get('token'))
        )) {
            $this->session->getFlashBag()->add('errors', 'Invalid token');
            return new RedirectResponse('/login');
        }

        $this->logInHandler->handle(new LogIn(
            (string)$request->get('nickname'),
            (string)$request->get('password')
        ));

        if ($this->session->get('userId') === null) {
            $this->session->getFlashBag()->add('errors', 'Invalid username or password');
            return new RedirectResponse('/login');
        }
        if ($this->session->get('userId') === '0') {
            $this->session->getFlashBag()->add('errors', 'Your Account is not active');
            return new RedirectResponse('/');
        }
        $this->session->set('nickname', $request->get('nickname'));
        $this->session->set('role', $request->cookies->get('role'));
        setcookie('nickname', $request->get('nickname'), 0, "/");
        // log login user
        $log_file = ROOT_DIR.'/logs/connections.txt';
        Analog::handler (File::init ($log_file));
        Analog::log($request->get('nickname').' connected', Analog::INFO);

        $this->session->getFlashBag()->add('success', 'You were logged in.');
        return new RedirectResponse('/');
    }

}