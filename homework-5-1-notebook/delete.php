<?php
declare(strict_types=1);

if (!defined('NOTEBOOK_APP')) {
    http_response_code(403);
    exit('Доступ запрещен');
}

function deleteContent(): string
{
    $message = '';
    $deleteId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($deleteId > 0) {
        $statement = db()->prepare('SELECT surname FROM contacts WHERE id = :id');
        $statement->execute([':id' => $deleteId]);
        $contact = $statement->fetch();

        if ($contact) {
            $delete = db()->prepare('DELETE FROM contacts WHERE id = :id');
            $delete->execute([':id' => $deleteId]);
            $message = '<p class="success">Запись с фамилией ' . e($contact['surname']) . ' удалена</p>';
        } else {
            $message = '<p class="error">Запись не найдена</p>';
        }
    }

    $contacts = db()
        ->query('SELECT id, surname, name, patronymic FROM contacts ORDER BY surname COLLATE NOCASE ASC, name COLLATE NOCASE ASC, id ASC')
        ->fetchAll();

    $html = '<section class="panel"><h2>Удаление записи</h2>' . $message;

    if ($contacts === []) {
        return $html . '<p class="empty">Нет записей для удаления.</p></section>';
    }

    $html .= '<div class="record-links delete-links">';
    foreach ($contacts as $contact) {
        $initials = firstLetter($contact['name']) . '.';
        if ($contact['patronymic'] !== '') {
            $initials .= firstLetter($contact['patronymic']) . '.';
        }
        $html .= '<a href="index.php?page=delete&amp;id=' . (int) $contact['id'] . '">' . e($contact['surname'] . ' ' . $initials) . '</a>';
    }
    $html .= '</div></section>';

    return $html;
}
