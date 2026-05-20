<?php
declare(strict_types=1);

define('NOTEBOOK_APP', true);

$title = '5.1. Домашняя работа: Notebook';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO('sqlite:' . __DIR__ . '/notebook.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            surname TEXT NOT NULL,
            name TEXT NOT NULL,
            patronymic TEXT NOT NULL DEFAULT "",
            gender TEXT NOT NULL,
            birth_date TEXT NOT NULL,
            phone TEXT NOT NULL,
            address TEXT NOT NULL,
            email TEXT NOT NULL,
            comment TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    if ($count === 0) {
        seedContacts($pdo);
    }

    return $pdo;
}

function seedContacts(PDO $pdo): void
{
    $contacts = [
        ['Иванов', 'Иван', 'Петрович', 'мужской', '1998-04-12', '+7 900 111-22-33', 'Москва, Ленина, 10', 'ivanov@example.com', 'Одногруппник'],
        ['Петрова', 'Анна', 'Сергеевна', 'женский', '2000-09-03', '+7 900 222-33-44', 'Москва, Мира, 15', 'petrova@example.com', 'Староста группы'],
        ['Сидоров', 'Павел', 'Игоревич', 'мужской', '1997-12-21', '+7 900 333-44-55', 'Химки, Центральная, 7', 'sidorov@example.com', 'Рабочий контакт'],
        ['Кузнецова', 'Мария', 'Олеговна', 'женский', '1999-01-30', '+7 900 444-55-66', 'Москва, Садовая, 18', 'kuznetsova@example.com', 'Подруга'],
        ['Смирнов', 'Алексей', 'Андреевич', 'мужской', '1996-06-18', '+7 900 555-66-77', 'Подольск, Кирова, 4', 'smirnov@example.com', 'Спортзал'],
        ['Попова', 'Елена', 'Викторовна', 'женский', '2001-11-09', '+7 900 666-77-88', 'Москва, Тверская, 22', 'popova@example.com', 'Проект'],
        ['Волков', 'Дмитрий', 'Николаевич', 'мужской', '1995-03-27', '+7 900 777-88-99', 'Королев, Гагарина, 9', 'volkov@example.com', 'Сосед'],
        ['Соколова', 'Ольга', 'Денисовна', 'женский', '1998-08-14', '+7 900 888-99-00', 'Москва, Арбат, 3', 'sokolova@example.com', 'Курсы'],
        ['Морозов', 'Никита', 'Романович', 'мужской', '2002-02-05', '+7 901 111-22-33', 'Мытищи, Новая, 11', 'morozov@example.com', 'Контакт из чата'],
        ['Новикова', 'Дарья', 'Ильинична', 'женский', '1994-07-25', '+7 901 222-33-44', 'Москва, Парковая, 6', 'novikova@example.com', 'Коллега'],
        ['Федоров', 'Кирилл', 'Максимович', 'мужской', '1993-10-16', '+7 901 333-44-55', 'Балашиха, Советская, 30', 'fedorov@example.com', 'Друг семьи'],
        ['Орлова', 'Полина', 'Артемовна', 'женский', '2000-05-19', '+7 901 444-55-66', 'Москва, Пушкина, 2', 'orlova@example.com', 'Учебный проект'],
    ];

    $statement = $pdo->prepare(
        'INSERT INTO contacts (surname, name, patronymic, gender, birth_date, phone, address, email, comment)
         VALUES (:surname, :name, :patronymic, :gender, :birth_date, :phone, :address, :email, :comment)'
    );

    foreach ($contacts as $contact) {
        $statement->execute([
            ':surname' => $contact[0],
            ':name' => $contact[1],
            ':patronymic' => $contact[2],
            ':gender' => $contact[3],
            ':birth_date' => $contact[4],
            ':phone' => $contact[5],
            ':address' => $contact[6],
            ':email' => $contact[7],
            ':comment' => $contact[8],
        ]);
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function firstLetter(string $value): string
{
    preg_match('/^./u', $value, $matches);
    return $matches[0] ?? '';
}

function currentPage(): string
{
    $allowed = ['view', 'add', 'edit', 'delete'];
    $page = isset($_GET['page']) ? (string) $_GET['page'] : 'view';

    return in_array($page, $allowed, true) ? $page : 'view';
}

function contactFieldsFromPost(): array
{
    return [
        'surname' => trim((string) ($_POST['surname'] ?? '')),
        'name' => trim((string) ($_POST['name'] ?? '')),
        'patronymic' => trim((string) ($_POST['patronymic'] ?? '')),
        'gender' => trim((string) ($_POST['gender'] ?? '')),
        'birth_date' => trim((string) ($_POST['birth_date'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'comment' => trim((string) ($_POST['comment'] ?? '')),
    ];
}

function validateContact(array $contact): array
{
    $errors = [];

    foreach (['surname' => 'Фамилия', 'name' => 'Имя', 'gender' => 'Пол', 'birth_date' => 'Дата рождения', 'phone' => 'Телефон', 'address' => 'Адрес', 'email' => 'E-mail'] as $field => $label) {
        if ($contact[$field] === '') {
            $errors[] = 'Поле "' . $label . '" обязательно для заполнения.';
        }
    }

    if ($contact['email'] !== '' && !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный E-mail.';
    }

    if ($contact['birth_date'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $contact['birth_date'])) {
        $errors[] = 'Введите дату рождения в корректном формате.';
    }

    if (!in_array($contact['gender'], ['мужской', 'женский'], true)) {
        $errors[] = 'Выберите пол из списка.';
    }

    return $errors;
}

function contactForm(array $contact, string $buttonText): string
{
    $gender = $contact['gender'] ?? '';

    return '<form class="contact-form" method="post">
        <label><span>Фамилия</span><input type="text" name="surname" value="' . e($contact['surname'] ?? '') . '" required></label>
        <label><span>Имя</span><input type="text" name="name" value="' . e($contact['name'] ?? '') . '" required></label>
        <label><span>Отчество</span><input type="text" name="patronymic" value="' . e($contact['patronymic'] ?? '') . '"></label>
        <label><span>Пол</span><select name="gender" required>
            <option value="">Выберите пол</option>
            <option value="мужской"' . ($gender === 'мужской' ? ' selected' : '') . '>мужской</option>
            <option value="женский"' . ($gender === 'женский' ? ' selected' : '') . '>женский</option>
        </select></label>
        <label><span>Дата рождения</span><input type="date" name="birth_date" value="' . e($contact['birth_date'] ?? '') . '" required></label>
        <label><span>Телефон</span><input type="text" name="phone" value="' . e($contact['phone'] ?? '') . '" required></label>
        <label><span>Адрес</span><input type="text" name="address" value="' . e($contact['address'] ?? '') . '" required></label>
        <label><span>E-mail</span><input type="email" name="email" value="' . e($contact['email'] ?? '') . '" required></label>
        <label class="wide"><span>Комментарий</span><textarea name="comment" rows="4">' . e($contact['comment'] ?? '') . '</textarea></label>
        <button class="form-btn" type="submit">' . e($buttonText) . '</button>
    </form>';
}

require_once __DIR__ . '/menu.php';
require_once __DIR__ . '/viewer.php';
require_once __DIR__ . '/add.php';
require_once __DIR__ . '/edit.php';
require_once __DIR__ . '/delete.php';

$page = currentPage();
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'created';
$paginationPage = max(1, (int) ($_GET['p'] ?? 1));

$content = match ($page) {
    'add' => addContent(),
    'edit' => editContent(),
    'delete' => deleteContent(),
    default => viewerContent($sort, $paginationPage),
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="page-header">
        <h1><?php echo e($title); ?></h1>
        <?php echo menu(); ?>
    </header>

    <main class="page-main">
        <?php echo $content; ?>
    </main>

    <footer class="page-footer">
        Записная книжка контактов
    </footer>
</body>
</html>
