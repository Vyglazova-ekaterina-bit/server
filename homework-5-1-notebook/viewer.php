<?php
declare(strict_types=1);

if (!defined('NOTEBOOK_APP')) {
    http_response_code(403);
    exit('Доступ запрещен');
}

function viewerContent(string $sort, int $page): string
{
    $allowedSorts = [
        'created' => 'id ASC',
        'surname' => 'surname COLLATE NOCASE ASC, name COLLATE NOCASE ASC, id ASC',
        'birth_date' => 'birth_date ASC, surname COLLATE NOCASE ASC, id ASC',
    ];

    if (!array_key_exists($sort, $allowedSorts)) {
        $sort = 'created';
    }

    $perPage = 10;
    $total = (int) db()->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min(max(1, $page), $totalPages);
    $offset = ($page - 1) * $perPage;

    $statement = db()->prepare('SELECT * FROM contacts ORDER BY ' . $allowedSorts[$sort] . ' LIMIT :limit OFFSET :offset');
    $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    $contacts = $statement->fetchAll();

    $html = '<section class="panel"><h2>Просмотр записной книжки</h2>';

    if ($contacts === []) {
        return $html . '<p class="empty">Записей пока нет.</p></section>';
    }

    $html .= '<div class="table-wrap"><table>
        <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Пол</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
                <th>Адрес</th>
                <th>E-mail</th>
                <th>Комментарий</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($contacts as $contact) {
        $html .= '<tr>
            <td>' . e($contact['surname']) . '</td>
            <td>' . e($contact['name']) . '</td>
            <td>' . e($contact['patronymic']) . '</td>
            <td>' . e($contact['gender']) . '</td>
            <td>' . e($contact['birth_date']) . '</td>
            <td>' . e($contact['phone']) . '</td>
            <td>' . e($contact['address']) . '</td>
            <td>' . e($contact['email']) . '</td>
            <td>' . e($contact['comment']) . '</td>
        </tr>';
    }

    $html .= '</tbody></table></div>';

    if ($totalPages > 1) {
        $html .= '<div class="pagination" aria-label="Пагинация">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $class = $i === $page ? ' class="active"' : '';
            $html .= '<a' . $class . ' href="index.php?page=view&amp;sort=' . e($sort) . '&amp;p=' . $i . '">' . $i . '</a>';
        }
        $html .= '</div>';
    }

    return $html . '</section>';
}
