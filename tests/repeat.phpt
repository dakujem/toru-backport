<?php

declare(strict_types=1);

use Tester\Assert;
use Tests\Support\Call;
use Tests\Support\DashTest;

require_once __DIR__ . '/../vendor/autoload.php';

(function () {
    DashTest::assert(
        [
            new Call(
                'repeat',
            ),
            new Call(
                'limit',
                3,
            ),
            new Call(
                'toArray',
            ),
        ],
        function ($result, ?string $desc) {
            Assert::same(
                [[1, 2, 42], [1, 2, 42], [1, 2, 42]],
                $result,
                $desc,
            );
        },
        [1, 2, 42],
        'repeats the whole [1,2,42] array for 3 times, creating a matrix, using repeat and limit',
    );

    DashTest::assert(
        [
            new Call(
                'replicate',
                3,
            ),
            new Call(
                'toArrayValues', // must be values only due to overlapping keys
            ),
        ],
        function ($result, ?string $desc) {
            Assert::same(
                [1, 2, 42, 1, 2, 42, 1, 2, 42],
                $result,
                $desc,
            );
        },
        [1, 2, 42],
        'repeats the elements of [1,2,42] array for 3 times, using replicate',
    );

    DashTest::assert(
        [
            new Call(
                'loop',
            ),
            new Call(
                'limit',
                3,
            ),
            new Call(
                'toArrayValues',
            ),
        ],
        function ($result, ?string $desc) {
            Assert::same(
                [1, 2, 42],
                $result,
                $desc,
            );
        },
        [1, 2, 42],
        'loops the elements of [1,2,42] array, limiting to 3 elements, using loop',
    );

    DashTest::assert(
        [
            new Call(
                'loop',
            ),
            new Call(
                'limit',
                10,
            ),
            new Call(
                'toArrayValues', // must be values only due to overlapping keys
            ),
        ],
        function ($result, ?string $desc) {
            Assert::same(
                [1, 2, 42, 1, 2, 42, 1, 2, 42, 1],
                $result,
                $desc,
            );
        },
        [1, 2, 42],
        'loops the elements of [1,2,42] array, limiting to 10 elements, using loop',
    );
})();


