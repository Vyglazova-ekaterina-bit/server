<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $filePath = __DIR__ . $requestedPath;

    if (is_file($filePath)) {
        return false;
    }
}

spl_autoload_register(function (string $className): void {
    $filePath = __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});

$route = $_GET['route'] ?? trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$routes = require __DIR__ . '/../src/routes.php';

$isRouteFound = false;
$matches = [];
$controllerAndAction = [];

foreach ($routes as $pattern => $currentControllerAndAction) {
    preg_match($pattern, $route, $currentMatches);

    if ($currentMatches !== []) {
        $isRouteFound = true;
        $matches = $currentMatches;
        $controllerAndAction = $currentControllerAndAction;
        break;
    }
}

if (!$isRouteFound) {
    http_response_code(404);
    echo 'Страница не найдена!';
    return;
}

unset($matches[0]);

$controllerName = $controllerAndAction[0];
$actionName = $controllerAndAction[1];

$controller = new $controllerName();
$controller->$actionName(...array_values($matches));
