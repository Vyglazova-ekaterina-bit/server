<?php
declare(strict_types=1);

if (!defined('NOTEBOOK_APP')) {
    http_response_code(403);
    exit('Доступ запрещен');
}

function menu(): string
{
    $activePage = currentPage();
    $activeSort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'created';
    $sorts = [
        'created' => 'По добавлению',
        'surname' => 'По фамилии',
        'birth_date' => 'По дате рождения',
    ];

    if (!array_key_exists($activeSort, $sorts)) {
        $activeSort = 'created';
    }

    $items = [
        'view' => 'Просмотр',
        'add' => 'Добавление записи',
        'edit' => 'Редактирование записи',
        'delete' => 'Удаление записи',
    ];

    $html = '<nav class="main-menu" aria-label="Основное меню">';

    foreach ($items as $page => $label) {
        $class = $activePage === $page ? ' class="active"' : '';
        $html .= '<a' . $class . ' href="index.php?page=' . e($page) . '">' . e($label) . '</a>';
    }

    $html .= '</nav>';

    if ($activePage === 'view') {
        $html .= '<nav class="sort-menu" aria-label="Сортировка">';
        foreach ($sorts as $sort => $label) {
            $class = $activeSort === $sort ? ' class="active"' : '';
            $html .= '<a' . $class . ' href="index.php?page=view&amp;sort=' . e($sort) . '">' . e($label) . '</a>';
        }
        $html .= '</nav>';
    }

    return $html;
}
