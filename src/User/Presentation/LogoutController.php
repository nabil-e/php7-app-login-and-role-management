<?php declare(strict_types=1);

namespace WinYum\User\Presentation;

use Analog\Analog;
use Analog\Handler\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LogoutController
{
    
    private $session;

    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    public function logout(): Response
    {
        // log logout the user
        $log_file = ROOT_DIR.'/logs/connections.txt';
        Analog::handler (File::init ($log_file));
        Analog::log($this->session->get('nickname').' disconnected', Analog::INFO);
        
        $this->session->clear();
        $this->session->getFlashBag()->add('success', 'Disconnected');

        return new RedirectResponse('/');
    }
}