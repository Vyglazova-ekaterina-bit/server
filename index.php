<?php
session_start();
date_default_timezone_set('Europe/Moscow');

$greetings = [
    'Hello, World!',
    'Привет, мир!',
    'Добро пожаловать на страницу PHP!',
    'Московский Политех приветствует вас!'
];

$currentGreeting = $greetings[array_rand($greetings)];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Домашняя работа: Hello, World!</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="page-header">
        <div class="logo" aria-label="Логотип МосПолитеха">
            <img src="mospolytech-logo.png" alt="Московский Политех">
        </div>
        <h1>Домашняя работа: Hello, World!</h1>
    </header>

    <main class="page-main">
        <section class="hello-card">
            <p class="eyebrow">Динамический контент на PHP</p>
            <h2><?php echo htmlspecialchars($currentGreeting); ?></h2>
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
