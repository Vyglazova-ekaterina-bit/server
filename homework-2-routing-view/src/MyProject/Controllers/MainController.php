<?php
declare(strict_types=1);

namespace MyProject\Controllers;

use MyProject\View\View;

class MainController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
    }

    public function main(): void
    {
        $articles = [
            ['name' => 'Статья 1', 'text' => 'Всем привет, это текст первой статьи'],
            ['name' => 'Статья 2', 'text' => 'Всем привет, это текст второй статьи'],
        ];

        $this->view->renderHtml('main/main.php', ['articles' => $articles]);
    }

    public function sayHello(string $name): void
    {
        $_SESSION['name'] = $name;

        $this->view->renderHtml('main/hello.php', [
            'name' => $name,
            'title' => 'Страница приветствия',
        ]);
    }

    public function sayBye(?string $name = null): void
    {
        if ($name !== null) {
            $_SESSION['name'] = $name;
        }

        $name = $name ?? $_SESSION['name'] ?? 'гость';

        $this->view->renderHtml('main/bye.php', ['name' => $name]);
    }
}
