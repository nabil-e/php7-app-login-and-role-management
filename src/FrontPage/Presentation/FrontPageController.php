<?php declare (strict_types = 1);

namespace WinYum\FrontPage\Presentation;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\HttpFoundation\Response;
use WinYum\Framework\Rendering\TemplateRenderer;
use WinYum\FrontPage\Application\SubmissionsQuery;

final class FrontPageController
{
    private $templateRenderer;
    private $submissionsQuery;

    public function __construct(TemplateRenderer $templateRenderer, SubmissionsQuery $submissionsQuery)
    {
        $this->templateRenderer = $templateRenderer;
        $this->submissionsQuery = $submissionsQuery;
    }
    
    public function show(): Response
    {
        $content = $this->templateRenderer->render('FrontPage.html.twig', [
            'submissions' => $this->submissionsQuery->execute(),
        ]);
        
        return new Response($content);
    }

    public function CheckSiteHealth () : Response {
        $content = "TODO: Site OK";
        return new Response($content);
    } 
}
