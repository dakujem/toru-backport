<?php

declare(strict_types=1);

use Dakujem\Toru\Dash;
use Dakujem\Toru\Exceptions\EmptyCollectionException;
use Dakujem\Toru\Exceptions\NoMatchingElementFound;
use Dakujem\Toru\Itera;
use Tester\Assert;
use Tests\Support\Call;
use Tests\Support\DashTest;

require_once __DIR__ . '/../vendor/autoload.php';

(function () {
    $input = [
        'a' => 'Adam',
        'b' => 'Betty',
        'c' => 'Claire',
        'd' => 'Daniel',
    ];

    DashTest::assert(
        [
            new Call('search', fn($v, $k) => $v === 'Claire'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('Claire', $out, $desc);
        },
        $input,
        'find Claire',
    );

    DashTest::assert(
        [
            new Call('search', fn($v, $k) => $k === 'b'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('Betty', $out, $desc);
        },
        $input,
        'find index "b"',
    );


    Assert::throws(fn() => Itera::searchOrFail([], fn() => false), NoMatchingElementFound::class);
    Assert::throws(fn() => Dash::collect([])->searchOrFail(fn() => false), NoMatchingElementFound::class);

    DashTest::assertThrows(
        [
            new Call(
                'searchOrFail',
                fn() => false, // always rejects, will not find anything
            ),
        ],
        function (callable $code): void {
            Assert::throws($code, NoMatchingElementFound::class, 'No element matching the search criteria found in the collection.');
        },
        $input,
    );

    DashTest::assertThrows(
        [
            new Call(
                'searchOrFail',
                fn() => true,
            ),
        ],
        function (callable $code): void {
            Assert::throws($code, NoMatchingElementFound::class, 'No element matching the search criteria found in the collection.');
        },
        [], // empty collection, nothing to be found
    );

    DashTest::assert(
        [
            new Call(
                'search',
                fn() => true,
            ),
        ],
        function ($out, ?string $desc): void {
            Assert::same(null, $out, $desc);
        },
        [],
        'nothing to be found in an empty collection, return null',
    );
    DashTest::assert(
        [
            new Call(
                'search',
                fn() => false,
            ),
        ],
        function ($out, ?string $desc): void {
            Assert::same(null, $out, $desc);
        },
        $input,
        'nothing to be found when the predicate rejects only, return null',
    );
})();


(function () {
    $input = [
        'a' => 'Adam',
        'b' => 'Betty',
        'c' => 'Claire',
        'd' => 'Daniel',
    ];

    DashTest::assert(
        [
            new Call('firstValue'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('Adam', $out, $desc);
        },
        $input,
        'first value is Adam',
    );
    DashTest::assert(
        [
            new Call('firstValueOrDefault', 'Frodo'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('Adam', $out, $desc);
        },
        $input,
        'first value is Adam',
    );
    DashTest::assert(
        [
            new Call('firstKey'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('a', $out, $desc);
        },
        $input,
        'first key is "a"',
    );
    DashTest::assert(
        [
            new Call('firstKeyOrDefault', 'Frodo'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('a', $out, $desc);
        },
        $input,
        'first key is "a"',
    );
    DashTest::assert(
        [
            new Call('omit', 2),
            new Call('firstKey'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('c', $out, $desc);
        },
        $input,
        'first key, when omitting the first two, is "c"',
    );

    DashTest::assert(
        [
            new Call('firstValueOrDefault', 'Frodo'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('Frodo', $out, $desc);
        },
        [],
        'empty? frodo!',
    );
    DashTest::assert(
        [
            new Call('firstKeyOrDefault', 'foo'),
        ],
        function ($out, ?string $desc): void {
            Assert::same('foo', $out, $desc);
        },
        [],
        'foo!',
    );

    DashTest::assertThrows(
        [
            new Call('firstValue'),
        ],
        function (callable $code): void {
            Assert::throws($code, EmptyCollectionException::class, 'Empty input collection encountered.');
        },
        [], // empty collection
    );
    DashTest::assertThrows(
        [
            new Call('firstKey'),
        ],
        function (callable $code): void {
            Assert::throws($code, EmptyCollectionException::class, 'Empty input collection encountered.');
        },
        [], // empty collection
    );
})();
