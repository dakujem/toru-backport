<?php

declare(strict_types=1);

use Dakujem\Toru\Itera;
use Tester\Assert;
use Tester\Environment;
use Tests\Support\Call;
use Tests\Support\DashTest;

require_once __DIR__ . '/../vendor/autoload.php';
Environment::setup();

(function () {
    DashTest::assert(
        [
            new Call(
                'slice',
                0,
                10_000_000,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        [],
        'Results in empty array anyway',
    );
    $input = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
    ];
    DashTest::assert(
        [
            new Call(
                'slice',
                0,
                count($input),
            ),
        ],
        function (iterable $result, ?string $desc) use ($input) {
            Assert::same($input, Itera::toArray($result), $desc);
        },
        $input,
        'Slice: full input.',
    );

    DashTest::assert(
        [
            new Call(
                'slice',
                0,
                count($input) - 1,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
                0 => 'zero',
                1 => 'one',
                2 => 'two',
//                3 => 'three',
            ], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: omit the last element',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                1,
                count($input),
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([
//                0 => 'zero',
                1 => 'one',
                2 => 'two',
                3 => 'three',
            ], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: omit the first element',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                count($input),
                PHP_INT_MAX,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: omit all elements',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                0,
                0,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: limit to zero',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                PHP_INT_MAX,
                0,
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: offset too far',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                0,
                -1 * (count($input)),
            ),
        ],
        function (iterable $result, ?string $desc) {
            Assert::same([], Itera::toArray($result), $desc);
        },
        $input,
        'Slice: negative limit results in zero elements',
    );
    DashTest::assert(
        [
            new Call(
                'slice',
                -1 * (count($input)),
                PHP_INT_MAX,
            ),
        ],
        function (iterable $result, ?string $desc) use ($input) {
            Assert::same($input, Itera::toArray($result), $desc);
        },
        $input,
        'Slice: negative offset behaves like zero offset, omits none',
    );
})();

(function () {
    $counter = 0;
    $iterable = Itera::limit(Itera::produce(function () use (&$counter) {
        $counter += 1;
        return 'foobar';
    }), 0);
    Assert::same([], Itera::toArray($iterable));
    Assert::same(0, $counter, 'the counter must not increment');
})();

(function () {
    $counter = 0;
    $iterable = Itera::limit(Itera::produce(function () use (&$counter) {
        $counter += 1;
        return 0;
    }), 2);
    Assert::same([0, 0], Itera::toArray($iterable));
    Assert::same(2, $counter, 'the counter must increment exactly 2 times');
})();

(function () {
    $counter = 0;
    $iterable = Itera::limit(Itera::produce(function () use (&$counter) {
        $counter += 1;
        return 0;
    }), 20);
    Assert::same(array_fill(0, 20, 0), Itera::toArray($iterable));
    Assert::same(20, $counter, 'the counter must increment exactly 20 times');
})();

// here we wrap the inner `limit` with the outer `omit`.
(function () {
    $counter = 0;
    $sequence = Itera::produce(function () use (&$counter) {
        $counter += 1;
        return 0;
    });
    $iterable = Itera::omit(Itera::limit($sequence, 20), 5);
    Assert::same(array_fill(5, 15, 0), Itera::toArray($iterable), 'the result must contain 15 elements starting with index 5');
    Assert::same(20, $counter, 'the counter must increment exactly 20 times');
})();

// (vice-versa) here we wrap the inner `omit` with the outer `limit`.
(function () {
    $counter = 0;
    $sequence = Itera::produce(function () use (&$counter) {
        $counter += 1;
        return 0;
    });
    $iterable = Itera::limit(Itera::omit($sequence, 5), 20);
    Assert::same(array_fill(5, 20, 0), Itera::toArray($iterable), 'the result must contain 20 elements (the limit) starting with index 5 (becasue the first 5 will have been skipped)');
    Assert::same(25, $counter, 'the counter must increment exactly 25 times');
})();

