<?php declare(strict_types=1);

namespace WinYum\Framework\Rendering;

interface TemplateRenderer
{
    public function render(string $template, array $data = []): string;
}