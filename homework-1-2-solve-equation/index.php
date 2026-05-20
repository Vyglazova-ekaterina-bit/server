<?php
$title = 'Домашняя работа: Solve the equation';
$equation = '7 / X = 2';

function parseExpression(string $expression): ?array
{
    $expression = str_replace(' ', '', strtoupper($expression));

    if (preg_match('/^([X]|-?\d+(?:\.\d+)?)([+\-*\/])([X]|-?\d+(?:\.\d+)?)$/', $expression, $matches)) {
        return [
            'left' => $matches[1],
            'operator' => $matches[2],
            'right' => $matches[3],
        ];
    }

    return null;
}

function solveEquation(string $equation): array
{
    $prepared = str_replace([' ', ';'], '', strtoupper($equation));
    $parts = explode('=', $prepared);

    if (count($parts) !== 2) {
        return ['error' => 'Уравнение должно содержать один знак равенства.'];
    }

    [$leftPart, $rightPart] = $parts;
    $unknownPart = str_contains($leftPart, 'X') ? $leftPart : $rightPart;
    $resultPart = str_contains($leftPart, 'X') ? $rightPart : $leftPart;
    $expression = parseExpression($unknownPart);

    if ($expression === null || !is_numeric($resultPart)) {
        return ['error' => 'Не удалось разобрать уравнение.'];
    }

    $operator = $expression['operator'];
    $xPosition = $expression['left'] === 'X' ? 'left' : 'right';
    $knownValue = (float) ($xPosition === 'left' ? $expression['right'] : $expression['left']);
    $resultValue = (float) $resultPart;

    if ($xPosition === 'left') {
        $x = match ($operator) {
            '+' => $resultValue - $knownValue,
            '-' => $resultValue + $knownValue,
            '*' => $knownValue == 0.0 ? null : $resultValue / $knownValue,
            '/' => $resultValue * $knownValue,
        };
    } else {
        $x = match ($operator) {
            '+' => $resultValue - $knownValue,
            '-' => $knownValue - $resultValue,
            '*' => $knownValue == 0.0 ? null : $resultValue / $knownValue,
            '/' => $resultValue == 0.0 ? null : $knownValue / $resultValue,
        };
    }

    if ($x === null) {
        return ['error' => 'Деление на ноль невозможно.'];
    }

    return [
        'operator' => $operator,
        'x_position' => $xPosition === 'left' ? 'слева от оператора' : 'справа от оператора',
        'x' => $x,
    ];
}

$solution = solveEquation($equation);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <main class="page">
        <section class="card">
            <p class="label">Вариант 3</p>
            <h1><?php echo $title; ?></h1>
            <p class="equation"><?php echo htmlspecialchars($equation); ?></p>

            <?php if (isset($solution['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($solution['error']); ?></p>
            <?php else: ?>
                <div class="grid">
                    <div>
                        <span>Оператор</span>
                        <strong><?php echo htmlspecialchars($solution['operator']); ?></strong>
                    </div>
                    <div>
                        <span>Положение неизвестной</span>
                        <strong><?php echo htmlspecialchars($solution['x_position']); ?></strong>
                    </div>
                    <div>
                        <span>Ответ</span>
                        <strong>X = <?php echo htmlspecialchars((string) $solution['x']); ?></strong>
                    </div>
                </div>

                <div class="solution">
                    <h2>Ход решения</h2>
                    <p>В уравнении <strong>6 / X = 2</strong> оператор: <strong>/</strong>.</p>
                    <p>Неизвестная переменная стоит справа от оператора, значит X является делителем.</p>
                    <p>Для вида <strong>A / X = B</strong> формула решения: <strong>X = A / B</strong>.</p>
                    <p><strong>X = 6 / 2 = 3</strong>.</p>
                </div>
            <?php endif; ?>

            <h2>Блок-схема алгоритма</h2>
            <img class="flowchart" src="flowchart.png" alt="Блок-схема алгоритма решения уравнения">
        </section>
    </main>
</body>
</html>
