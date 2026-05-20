<?php
declare(strict_types=1);

return [
    '~^hello/([a-zA-Zа-яА-ЯёЁ0-9_-]+)$~u' => [\MyProject\Controllers\MainController::class, 'sayHello'],
    '~^bye/([a-zA-Zа-яА-ЯёЁ0-9_-]+)$~u' => [\MyProject\Controllers\MainController::class, 'sayBye'],
    '~^$~' => [\MyProject\Controllers\MainController::class, 'main'],
];
