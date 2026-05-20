<?php
$title = '2.2. Домашняя работа: Calculator';
$expressionFromGet = isset($_GET['expression']) ? (string) $_GET['expression'] : '';
$resultFromGet = isset($_GET['result']) ? (string) $_GET['result'] : '';
$errorFromGet = isset($_GET['error']) ? (string) $_GET['error'] : '';

function normalizeExpression(string $expression): string
{
    $expression = strtolower(trim($expression));
    $expression = str_replace([' ', "\t", "\n", "\r", ','], ['', '', '', '', '.'], $expression);
    $expression = str_replace(['×', '÷', 'π'], ['*', '/', 'pi'], $expression);
    return $expression;
}

function formatNumber(float $number): string
{
    if (is_nan($number) || is_infinite($number)) {
        throw new RuntimeException('Результат вычисления не является числом.');
    }

    if (abs($number - round($number)) < 0.0000000001) {
        return (string) round($number);
    }

    return rtrim(rtrim(sprintf('%.10F', $number), '0'), '.');
}

function recursiveFactorial(float $number): float
{
    if ($number < 0 || abs($number - round($number)) > 0.0000000001) {
        throw new InvalidArgumentException('Факториал можно вычислить только для неотрицательного целого числа.');
    }

    $integer = (int) round($number);

    if ($integer > 170) {
        throw new InvalidArgumentException('Слишком большое число для факториала.');
    }

    if ($integer <= 1) {
        return 1;
    }

    return $integer * recursiveFactorial($integer - 1);
}

function addValues(float $left, float $right): float
{
    return $left + $right;
}

function subtractValues(float $left, float $right): float
{
    return $left - $right;
}

function multiplyValues(float $left, float $right): float
{
    return $left * $right;
}

function divideValues(float $left, float $right): float
{
    if (abs($right) < 0.0000000001) {
        throw new InvalidArgumentException('Деление на ноль невозможно.');
    }

    return $left / $right;
}

function powerValues(float $left, float $right): float
{
    return $left ** $right;
}

final class CalculatorParser
{
    private string $expression;
    private int $position = 0;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public function calculate(): float
    {
        if ($this->expression === '') {
            throw new InvalidArgumentException('Введите выражение.');
        }

        if (preg_match('/[^0-9+\-*\/^().!a-z]/', $this->expression)) {
            throw new InvalidArgumentException('Выражение содержит недопустимые символы.');
        }

        $result = $this->parseExpression();

        if (!$this->isEnd()) {
            throw new InvalidArgumentException('Не удалось разобрать выражение около "' . $this->current() . '".');
        }

        return $result;
    }

    private function parseExpression(): float
    {
        $result = $this->parseTerm();

        while (!$this->isEnd() && ($this->current() === '+' || $this->current() === '-')) {
            $operator = $this->consume();
            $right = $this->parseTerm();
            $result = $operator === '+'
                ? addValues($result, $right)
                : subtractValues($result, $right);
        }

        return $result;
    }

    private function parseTerm(): float
    {
        $result = $this->parseUnary();

        while (!$this->isEnd() && ($this->current() === '*' || $this->current() === '/')) {
            $operator = $this->consume();
            $right = $this->parseUnary();
            $result = $operator === '*'
                ? multiplyValues($result, $right)
                : divideValues($result, $right);
        }

        return $result;
    }

    private function parsePower(): float
    {
        $result = $this->parsePostfix();

        if (!$this->isEnd() && $this->current() === '^') {
            $this->consume();
            $right = $this->parseUnary();
            $result = powerValues($result, $right);
        }

        return $result;
    }

    private function parseUnary(): float
    {
        if ($this->match('+')) {
            return $this->parseUnary();
        }

        if ($this->match('-')) {
            return -$this->parseUnary();
        }

        if ($this->matchWord('sqrt')) {
            $value = $this->parseFunctionArgument();
            if ($value < 0) {
                throw new InvalidArgumentException('Корень из отрицательного числа не вычисляется в действительных числах.');
            }
            return sqrt($value);
        }

        if ($this->matchWord('ln')) {
            $value = $this->parseFunctionArgument();
            if ($value <= 0) {
                throw new InvalidArgumentException('ln можно вычислить только для положительного числа.');
            }
            return log($value);
        }

        if ($this->matchWord('log')) {
            $value = $this->parseFunctionArgument();
            if ($value <= 0) {
                throw new InvalidArgumentException('log можно вычислить только для положительного числа.');
            }
            return log10($value);
        }

        return $this->parsePower();
    }

    private function parsePostfix(): float
    {
        $result = $this->parsePrimary();

        while ($this->match('!')) {
            $result = recursiveFactorial($result);
        }

        return $result;
    }

    private function parsePrimary(): float
    {
        if ($this->match('(')) {
            $result = $this->parseExpression();
            if (!$this->match(')')) {
                throw new InvalidArgumentException('Не хватает закрывающей скобки.');
            }
            return $result;
        }

        if ($this->matchWord('pi')) {
            return pi();
        }

        if ($this->matchWord('e')) {
            return exp(1);
        }

        return $this->parseNumber();
    }

    private function parseFunctionArgument(): float
    {
        if ($this->match('(')) {
            $result = $this->parseExpression();
            if (!$this->match(')')) {
                throw new InvalidArgumentException('Не хватает закрывающей скобки после функции.');
            }
            return $result;
        }

        return $this->parseUnary();
    }

    private function parseNumber(): float
    {
        $start = $this->position;
        $hasDot = false;

        while (!$this->isEnd()) {
            $char = $this->current();

            if ($char === '.') {
                if ($hasDot) {
                    break;
                }
                $hasDot = true;
                $this->position++;
                continue;
            }

            if (!ctype_digit($char)) {
                break;
            }

            $this->position++;
        }

        if ($start === $this->position || substr($this->expression, $start, $this->position - $start) === '.') {
            throw new InvalidArgumentException('Ожидалось число, функция или скобка.');
        }

        return (float) substr($this->expression, $start, $this->position - $start);
    }

    private function match(string $expected): bool
    {
        if ($this->current() !== $expected) {
            return false;
        }

        $this->position++;
        return true;
    }

    private function matchWord(string $word): bool
    {
        $length = strlen($word);

        if (substr($this->expression, $this->position, $length) !== $word) {
            return false;
        }

        $next = substr($this->expression, $this->position + $length, 1);
        if ($next !== '' && ctype_alpha($next)) {
            return false;
        }

        $this->position += $length;
        return true;
    }

    private function consume(): string
    {
        return $this->expression[$this->position++];
    }

    private function current(): string
    {
        return $this->expression[$this->position] ?? '';
    }

    private function isEnd(): bool
    {
        return $this->position >= strlen($this->expression);
    }
}

function calculateExpression(string $expression): string
{
    $parser = new CalculatorParser(normalizeExpression($expression));
    return formatNumber($parser->calculate());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedExpression = isset($_POST['expression']) ? (string) $_POST['expression'] : '';
    $params = ['expression' => $postedExpression];

    try {
        $params['result'] = calculateExpression($postedExpression);
    } catch (Throwable $exception) {
        $params['error'] = $exception->getMessage();
    }

    header('Location: index.php?' . http_build_query($params));
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header class="page-header">
        <h1><?php echo htmlspecialchars($title); ?></h1>
    </header>

    <main class="page-main">
        <section class="calculator" aria-label="Калькулятор">
            <form class="calculator-form" method="post" autocomplete="off">
                <label class="display-label" for="expression">Выражение</label>
                <input
                    class="display"
                    id="expression"
                    name="expression"
                    type="text"
                    inputmode="decimal"
                    value="<?php echo htmlspecialchars($expressionFromGet); ?>"
                    placeholder="0"
                    aria-describedby="calculator-message"
                >

                <div class="result-line" id="calculator-message" aria-live="polite">
                    <?php if ($errorFromGet !== ''): ?>
                        <span class="error"><?php echo htmlspecialchars($errorFromGet); ?></span>
                    <?php elseif ($resultFromGet !== ''): ?>
                        <span>Результат: <strong><?php echo htmlspecialchars($resultFromGet); ?></strong></span>
                    <?php else: ?>
                        <span>Введите выражение кнопками или с клавиатуры</span>
                    <?php endif; ?>
                </div>

                <div class="keys">
                    <button type="button" data-clear>AC</button>
                    <button type="button" data-value="(">(</button>
                    <button type="button" data-value=")">)</button>
                    <button type="button" data-value="/">/</button>

                    <button type="button" data-value="7">7</button>
                    <button type="button" data-value="8">8</button>
                    <button type="button" data-value="9">9</button>
                    <button type="button" data-value="*">*</button>

                    <button type="button" data-value="4">4</button>
                    <button type="button" data-value="5">5</button>
                    <button type="button" data-value="6">6</button>
                    <button type="button" data-value="-">-</button>

                    <button type="button" data-value="1">1</button>
                    <button type="button" data-value="2">2</button>
                    <button type="button" data-value="3">3</button>
                    <button type="button" data-value="+">+</button>

                    <button type="button" data-value="0">0</button>
                    <button type="button" data-value=".">.</button>
                    <button type="button" data-value="^">^</button>
                    <button type="submit" class="equals">=</button>

                    <button type="button" data-value="sqrt(">sqrt</button>
                    <button type="button" data-value="ln(">ln</button>
                    <button type="button" data-value="log(">log</button>
                    <button type="button" data-value="!">!</button>

                    <button type="button" data-value="pi">pi</button>
                    <button type="button" data-value="e">e</button>
                    <button type="button" data-backspace>⌫</button>
                    <button type="button" data-value="-(">-()</button>
                </div>
            </form>
        </section>
    </main>

    <footer class="page-footer">
        POST-параметр: expression, результат возвращается через GET-параметр
    </footer>
</body>
</html>
