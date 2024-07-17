<?php

declare(strict_types=1);

use Dakujem\Toru\Dash;
use Dakujem\Toru\Itera;
use Dakujem\Toru\IteraFn;
use Tester\Assert;
use Tests\Support\Call;
use Tests\Support\DashTest;

require_once __DIR__ . '/../vendor/autoload.php';


(function () {
    DashTest::assert(
        [
            new Call(
                'adjust',
                fn($v, $k) => 'the value is ' . $v . ' and the key is ' . $k,
                fn($v, $k) => 'the key is ' . $k . ' and the value is ' . $v,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        [],
        'Results in empty array anyway',
    );

    DashTest::assert(
        [
            new Call(
                'adjust',
                fn($v, $k) => 'the value is ' . $v . ' and the key is ' . $k,
                fn($v, $k) => 'the key is ' . $k . ' and the value is ' . $v,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
                'the key is 0 and the value is zero' => 'the value is zero and the key is 0',
                'the key is 1 and the value is one' => 'the value is one and the key is 1',
                'the key is 2 and the value is two' => 'the value is two and the key is 2',
                'the key is 3 and the value is three' => 'the value is three and the key is 3',
            ], Itera::toArray($result), $desc);
        },
        ['zero', 'one', 'two', 'three',],
        'Should map values and keys based on values and keys',
    );
})();

(function () {
    DashTest::assert(
        [
            new Call(
                'apply',
                fn() => 'whateva',
            ),
            new Call(
                'reindex',
                fn() => 'meh',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        [],
        'Results in empty array anyway',
    );

    // Note that calling `reindex` and `apply` in a chain is not the same as calling `adjust`, because of the keys/values being updated by the method that comes prior.
    DashTest::assert(
        [
            new Call(
                'reindex',
                fn($v, $k) => 'the key for ' . $v . ' is ' . $k,
            ),
            new Call(
                'apply',
                fn($v, $k) => 'but the key for ' . $v . ' has since changed to "' . $k . '"',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    'the key for zero is 0' => 'but the key for zero has since changed to "the key for zero is 0"',
                    'the key for one is 1' => 'but the key for one has since changed to "the key for one is 1"',
                    'the key for two is 2' => 'but the key for two has since changed to "the key for two is 2"',
                    'the key for three is 3' => 'but the key for three has since changed to "the key for three is 3"',
                ],
                Itera::toArray($result),
                $desc,
            );
        },
        ['zero', 'one', 'two', 'three',],
        'Should map values and keys based on values and keys, separately',
    );

    // Actually we may use the same test for `adjust`.
    DashTest::assert(
        [
            new Call(
                'adjust',
                null,
                fn($v, $k) => 'the key for ' . $v . ' is ' . $k,
            ),
            new Call(
                'adjust',
                fn($v, $k) => 'but the key for ' . $v . ' has since changed to "' . $k . '"',
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    'the key for zero is 0' => 'but the key for zero has since changed to "the key for zero is 0"',
                    'the key for one is 1' => 'but the key for one has since changed to "the key for one is 1"',
                    'the key for two is 2' => 'but the key for two has since changed to "the key for two is 2"',
                    'the key for three is 3' => 'but the key for three has since changed to "the key for three is 3"',
                ],
                Itera::toArray($result),
                $desc,
            );
        },
        ['zero', 'one', 'two', 'three',],
        'adjusting keys and then adjusting values separately',
    );
})();

(function () {
    // Calling `adjust` without parameters returns the same iterable.
    $input = Itera::make('zero', 'one', 'two', 'three');
    DashTest::assert(
        [
            new Call(
                'adjust',
            ),
        ],
        function (iterable $result, ?string $desc) use ($input) {
            Assert::same(
                $input,
                $result,
                $desc,
            );
        },
        $input,
        'Should map values and keys based on values and keys',
        [Itera::class, IteraFn::class],
    );

    $array = Itera::toArray($input);
    Assert::same(
        $array,
        Dash::collect($array)->adjust()->toArray(),
    );
})();

(function () {
    // `map` and `apply` do the same
    DashTest::assert(
        [
            new Call(
                'map',
                fn($v, $k) => 'the value at ' . $k . ' is ' . $v,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    0 => 'the value at 0 is zero',
                    1 => 'the value at 1 is one',
                    2 => 'the value at 2 is two',
                    3 => 'the value at 3 is three',
                ],
                Itera::toArray($result),
                $desc,
            );
        },
        ['zero', 'one', 'two', 'three',],
        'Should map values based on values and keys',
    );
    DashTest::assert(
        [
            new Call(
                'apply',
                fn($v, $k) => 'the value at ' . $k . ' is ' . $v,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    0 => 'the value at 0 is zero',
                    1 => 'the value at 1 is one',
                    2 => 'the value at 2 is two',
                    3 => 'the value at 3 is three',
                ],
                Itera::toArray($result),
                $desc,
            );
        },
        ['zero', 'one', 'two', 'three',],
        'Should map values based on values and keys',
    );
})();

(function () {
    /* @see Itera::unfold() */
    DashTest::assert(
        [
            new Call(
                'unfold',
                fn($v, $k): iterable => Itera::limit(Itera::repeat($v), $k), // the mapper returns an iterable
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    // 'zero' -- zero is repeated zero times, that means it is omitted
                    'one',
                    'two',
                    'two',
                    'three',
                    'three',
                    'three',
                ],
                Itera::toArrayValues($result), // values only, the keys overlap
                $desc,
            );
        },
        [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
        ],
        'Repeat each value key-number of times.',
    );

    // use unfold to map keys and values using the same callable
    DashTest::assert(
        [
            new Call(
                'unfold',
                function (string $v): iterable {
                    $pieces = explode(':', $v);
                    // We may choose to either return an array with a single element or yield once,
                    // it results in the same transformation.
                    yield $pieces[0] => $pieces[1];
                },
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same(
                [
                    'a' => 'Adam',
                    'b' => 'Betty',
                    'c' => 'Claire',
                    'd' => 'Daniel',
                ],
                Itera::toArray($result),
                $desc,
            );
        },
        [
            'a:Adam',
            'b:Betty',
            'c:Claire',
            'd:Daniel',
        ],
        'Using a single callable, break the value in two parts and use them to create a new collection, specifying keys and values',
    );
})();
