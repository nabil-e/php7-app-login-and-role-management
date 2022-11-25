<?php declare(strict_types=1);

namespace WinYum\Framework\Rendering;

use Twig;
use Twig\Loader\FilesystemLoader;
use WinYum\Framework\Csrf\StoredTokenReader;
use WinYum\Framework\Rendering\TemplateDirectory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class TwigTemplateRendererFactory
{
    
    private $storedTokenReader;
    private $templateDirectory;
    private $session;

    public function __construct(
        TemplateDirectory $templateDirectory,
        StoredTokenReader $storedTokenReader,
        Session $session
    ) {
        $this->templateDirectory = $templateDirectory;
        $this->storedTokenReader = $storedTokenReader;
        $this->session = $session;
    }

    public function create(): TwigTemplateRenderer
    {
        $loader = new FilesystemLoader([
            $this->templateDirectory->toString(),
        ]);
        $twigEnvironment = new Twig\Environment($loader);

        $twigEnvironment->addGlobal('session', $this->session);

        $twigEnvironment->addFunction(
            new Twig\TwigFunction('get_token', function (string $key): string {
                $token = $this->storedTokenReader->read($key);
                return $token->toString();
            })
        );

        $twigEnvironment->addFunction(
            new Twig\TwigFunction('get_flash_bag', function (): FlashBagInterface {
                return $this->session->getFlashBag();
            })
        );

        return new TwigTemplateRenderer($twigEnvironment);
    }
}