<?php
declare(strict_types=1);

namespace MyProject\View;

class View
{
    private string $templatesPath;

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    public function renderHtml(string $templateName, array $vars = []): void
    {
        extract($vars);

        include $this->templatesPath . '/' . $templateName;
    }
}
