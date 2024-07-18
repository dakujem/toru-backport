<?php

declare(strict_types=1);

use Dakujem\Toru\Itera;
use Tester\Assert;

require_once __DIR__ . '/../vendor/autoload.php';

class Bar implements IteratorAggregate
{
    public function getIterator(): Traversable
    {
        return new ArrayIterator([1, 4, 5, 3, 2]);
    }
}

class Foo extends Bar implements Countable
{
    public function count(): int
    {
        return 42;
    }
}

(function () {
    // Foo is countable
    $foo = new Foo();
    Assert::same(5, iterator_count($foo));
    Assert::same(42, count($foo));
    Assert::same(42, Itera::count($foo));

    // ... while Bar is not.
    $bar = new Bar();
    Assert::same(5, iterator_count($bar));

    if (version_compare(PHP_VERSION, '7.0.0', '>=') && version_compare(PHP_VERSION, '8.0.0', '<')) {
        // PHP 7
        Assert::error(fn() => count($bar), E_WARNING);
    } elseif (version_compare(PHP_VERSION, '8.0.0', '>=')) {
        // PHP 8
        Assert::throws(fn() => count($bar), TypeError::class);
    } else {
        throw new LogicException();
    }

    Assert::same(5, Itera::count($bar));
})();

(function () {
    $input = [
        'a' => 'Adam',
        'b' => 'Betty',
        'c' => 'Claire',
        'd' => 'Daniel',
    ];
    $iterable = Itera::make(...array_values($input));
    Assert::same(count($input), Itera::count($iterable));
})();
