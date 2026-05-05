<?php
$title = 'Домашняя работа: Feedback Form';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
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
            <form action="https://httpbin.org/post" method="post" class="feedback-form">
                <label>
                    <span>Имя пользователя</span>
                    <input type="text" name="username" placeholder="Введите имя" required>
                </label>
                <label>
                    <span>e-mail пользователя</span>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </label>
                <label>
                    <span>Тип обращения</span>
                    <select name="request_type" required>
                        <option value="">Выберите тип обращения</option>
                        <option value="complaint">Жалоба</option>
                        <option value="suggestion">Предложение</option>
                        <option value="thanks">Благодарность</option>
                    </select>
                </label>
                <label>
                    <span>Текст обращения</span>
                    <textarea name="message" rows="7" placeholder="Напишите текст обращения" required></textarea>
                </label>
                <fieldset>
                    <legend>Вариант ответа</legend>
                    <label class="checkbox-row">
                        <input type="checkbox" name="reply[]" value="sms">
                        <span>смс</span>
                    </label>
                    <label class="checkbox-row">
                        <input type="checkbox" name="reply[]" value="email">
                        <span>e-mail</span>
                    </label>
                </fieldset>
                <div class="actions">
                    <button type="submit">Отправить</button>
                    <a class="secondary-link" href="index2.php">Перейти на 2 страницу</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="page-footer">
        задание для самостоятельно работы
    </footer>
</body>
</html>
