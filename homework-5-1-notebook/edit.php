<?php
declare(strict_types=1);

if (!defined('NOTEBOOK_APP')) {
    http_response_code(403);
    exit('Доступ запрещен');
}

function editContent(): string
{
    $contacts = db()
        ->query('SELECT id, surname, name FROM contacts ORDER BY surname COLLATE NOCASE ASC, name COLLATE NOCASE ASC, id ASC')
        ->fetchAll();

    if ($contacts === []) {
        return '<section class="panel"><h2>Редактирование записи</h2><p class="empty">Нет записей для редактирования.</p></section>';
    }

    $ids = array_map(static fn (array $contact): int => (int) $contact['id'], $contacts);
    $currentId = (int) ($_GET['id'] ?? $ids[0]);
    if (!in_array($currentId, $ids, true)) {
        $currentId = $ids[0];
    }

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contact = contactFieldsFromPost();
        $errors = validateContact($contact);

        if ($errors === []) {
            try {
                $statement = db()->prepare(
                    'UPDATE contacts
                     SET surname = :surname, name = :name, patronymic = :patronymic, gender = :gender,
                         birth_date = :birth_date, phone = :phone, address = :address, email = :email, comment = :comment
                     WHERE id = :id'
                );
                $statement->execute([
                    ':surname' => $contact['surname'],
                    ':name' => $contact['name'],
                    ':patronymic' => $contact['patronymic'],
                    ':gender' => $contact['gender'],
                    ':birth_date' => $contact['birth_date'],
                    ':phone' => $contact['phone'],
                    ':address' => $contact['address'],
                    ':email' => $contact['email'],
                    ':comment' => $contact['comment'],
                    ':id' => $currentId,
                ]);
                $message = '<p class="success">Запись обновлена</p>';
            } catch (Throwable) {
                $message = '<p class="error">Ошибка: запись не обновлена</p>';
            }
        } else {
            $message = '<p class="error">Ошибка: запись не обновлена</p><ul class="errors"><li>' . implode('</li><li>', array_map('e', $errors)) . '</li></ul>';
        }
    }

    $statement = db()->prepare('SELECT * FROM contacts WHERE id = :id');
    $statement->execute([':id' => $currentId]);
    $currentContact = $statement->fetch();

    if (!$currentContact) {
        return '<section class="panel"><h2>Редактирование записи</h2><p class="error">Запись не найдена.</p></section>';
    }

    $links = '<div class="record-links">';
    foreach ($contacts as $contact) {
        $class = (int) $contact['id'] === $currentId ? ' class="current"' : '';
        $links .= '<a' . $class . ' href="index.php?page=edit&amp;id=' . (int) $contact['id'] . '">' . e($contact['surname'] . ' ' . $contact['name']) . '</a>';
    }
    $links .= '</div>';

    return '<section class="panel"><h2>Редактирование записи</h2>' . $links . $message . contactForm($currentContact, 'Сохранить изменения') . '</section>';
}
