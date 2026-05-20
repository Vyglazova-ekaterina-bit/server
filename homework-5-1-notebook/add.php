<?php
declare(strict_types=1);

if (!defined('NOTEBOOK_APP')) {
    http_response_code(403);
    exit('Доступ запрещен');
}

function addContent(): string
{
    $message = '';
    $contact = [
        'surname' => '',
        'name' => '',
        'patronymic' => '',
        'gender' => '',
        'birth_date' => '',
        'phone' => '',
        'address' => '',
        'email' => '',
        'comment' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contact = contactFieldsFromPost();
        $errors = validateContact($contact);

        if ($errors === []) {
            try {
                $statement = db()->prepare(
                    'INSERT INTO contacts (surname, name, patronymic, gender, birth_date, phone, address, email, comment)
                     VALUES (:surname, :name, :patronymic, :gender, :birth_date, :phone, :address, :email, :comment)'
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
                ]);
                $message = '<p class="success">Запись добавлена</p>';
                $contact = array_fill_keys(array_keys($contact), '');
            } catch (Throwable) {
                $message = '<p class="error">Ошибка: запись не добавлена</p>';
            }
        } else {
            $message = '<p class="error">Ошибка: запись не добавлена</p><ul class="errors"><li>' . implode('</li><li>', array_map('e', $errors)) . '</li></ul>';
        }
    }

    return '<section class="panel"><h2>Добавление записи</h2>' . $message . contactForm($contact, 'Добавить запись') . '</section>';
}
