<?php
declare(strict_types=1);

interface CalculateSquare
{
    public function calculateSquare(): float;
}

class Rectangle implements CalculateSquare
{
    private float $width;
    private float $height;

    public function __construct(float $width, float $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function calculateSquare(): float
    {
        return $this->width * $this->height;
    }
}

class Circle implements CalculateSquare
{
    private float $radius;

    public function __construct(float $radius)
    {
        $this->radius = $radius;
    }

    public function calculateSquare(): float
    {
        return pi() * $this->radius ** 2;
    }
}

class Car
{
    private string $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }
}

function printSquareInfo(object $object): void
{
    $className = get_class($object);

    if (!$object instanceof CalculateSquare) {
        echo 'Объект класса ' . $className . ' не реализует интерфейс CalculateSquare.' . PHP_EOL;
        return;
    }

    echo 'Объект класса ' . $className . ' реализует интерфейс CalculateSquare.' . PHP_EOL;
    echo 'Площадь: ' . round($object->calculateSquare(), 2) . PHP_EOL;
}

$objects = [
    new Rectangle(10, 5),
    new Circle(3),
    new Car('Toyota'),
];

foreach ($objects as $object) {
    printSquareInfo($object);
    echo PHP_EOL;
}
