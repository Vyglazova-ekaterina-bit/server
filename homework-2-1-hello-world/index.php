<?php
session_start();

$title = 'Домашняя работа: Hello, World!';
$greetings = [
    'Hello, World!',
    'Привет, мир!',
    'Добро пожаловать на страницу PHP!',
    'Московский Политех приветствует вас!'
];

$greeting = $greetings[array_rand($greetings)];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="page-header">
        <a class="logo" href="hello-world.php" aria-label="Московский Политех">
            <img src="mospolytech-logo.png" alt="Московский Политех">
        </a>
        <h1><?php echo $title; ?></h1>
    </header>

    <main class="page-main">
        <section class="panel">
            <p class="label">Динамический контент на PHP</p>
            <h2><?php echo htmlspecialchars($greeting); ?></h2>
            <form method="post">
                <button type="submit">Обновить приветствие</button>
            </form>
        </section>
    </main>

    <footer class="page-footer">
        задание для самостоятельной работы
    </footer>
</body>
</html>
