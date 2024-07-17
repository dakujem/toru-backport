<?php

declare(strict_types=1);

use Dakujem\Toru\Itera;
use Tester\Assert;
use Tests\Support\Call;
use Tests\Support\DashTest;

require_once __DIR__ . '/../vendor/autoload.php';


(function () {
    DashTest::assert(
        [
            new Call(
                'keysOnly',
            ),
            new Call(
                'valuesOnly',
            ),
            new Call(
                'flip',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        [],
        'Results in empty array anyway',
    );

    $input = [
        'a' => 'Adam',
        'b' => 'Betty',
        'c' => 'Claire',
        'd' => 'Daniel',
    ];
    DashTest::assert(
        [
            new Call(
                'keysOnly',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
                'a',
                'b',
                'c',
                'd',
            ], Itera::toArray($result), $desc);
        },
        $input,
        'keys only',
    );
    DashTest::assert(
        [
            new Call(
                'valuesOnly',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
                'Adam',
                'Betty',
                'Claire',
                'Daniel',
            ], Itera::toArray($result), $desc);
        },
        $input,
        'values only',
    );
    DashTest::assert(
        [
            new Call(
                'flip',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
                'Adam' => 'a',
                'Betty' => 'b',
                'Claire' => 'c',
                'Daniel' => 'd',
            ], Itera::toArray($result), $desc);
        },
        $input,
        'flip | pilf',
    );
})();

