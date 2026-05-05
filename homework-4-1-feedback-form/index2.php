<?php
$title = 'Домашняя работа: Feedback Form';
$headers = @get_headers('https://httpbin.org/post');

if ($headers === false) {
    $headers = @get_headers('http://httpbin.org/post');
}

$headersText = $headers === false
    ? 'Не удалось получить заголовки. Проверьте подключение к интернету и настройки PHP.'
    : implode(PHP_EOL, $headers);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - get_headers</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <header class="page-header">
        <a class="logo" href="index.php" aria-label="Московский Политех">
            <img src="mospolytech-logo.png" alt="Московский Политех">
        </a>
        <h1><?php echo $title; ?></h1>
    </header>

    <main class="page-main">
        <section class="panel">
            <label>
                <span>Результат работы функции get_headers</span>
                <textarea class="headers-output" rows="14" readonly><?php echo htmlspecialchars($headersText); ?></textarea>
            </label>
            <div class="actions">
                <a class="secondary-link" href="index.php">Вернуться на 1 страницу</a>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        задание для самостоятельно работы
    </footer>
</body>
</html>
