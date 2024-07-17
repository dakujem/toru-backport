<?php

declare(strict_types=1);

namespace Tests\Support;

use Dakujem\Toru\Dash;
use Dakujem\Toru\Itera;
use Dakujem\Toru\IteraFn;
use Dakujem\Toru\Pipeline;
use LogicException;

/**
 * Allows for testing all the iteration implementations of `dakujem/toru`
 * (`Itera`, `IteraFn`, `Dash`) using unified commands.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class DashTest
{
    /**
     * @param Call[] $callChain
     */
    public static function assertDash(
        iterable $callChain,
        callable $assertion,
        $input,
        ?string $description = null
    ): void {
        $assertion(
            self::invokeDash($callChain, $input),
            $description,
        );
    }

    /**
     * @param Call[] $callChain
     */
    public static function invokeDash(
        iterable $callChain,
        $input
    ) {
        if (!is_iterable($input)) {
            throw new LogicException('Dash has to be used with iterable collection. Build a separate test for Dash class and use the $subjects parameter of `assert` method to limit the subjects of this test.');
        }
        $collection = new Dash($input);
        $i = 0;
        foreach ($callChain as $call) {
            $collection = $collection->{$call->method()}(...$call->args());
            $i += 1;
        }
        if ($i <= 0) {
            throw new LogicException('Empty call chain.');
        }
        return $collection;
    }

    /**
     * @param Call[] $callChain
     */
    public static function assertItera(
        iterable $callChain,
        callable $assertion,
        $input,
        ?string $description = null
    ): void {
        $assertion(
            self::invokeItera($callChain, $input),
            $description,
        );
    }

    /**
     * @param Call[] $callChain
     */
    public static function invokeItera(
        iterable $callChain,
        $input
    ) {
        $i = 0;
        foreach ($callChain as $call) {
            $input = Itera::{$call->method()}($input, ...$call->args());
            $i += 1;
        }
        if ($i <= 0) {
            throw new LogicException('Empty call chain.');
        }
        return $input;
    }

    /**
     * @param Call[] $callChain
     */
    public static function assertIteraFn(
        iterable $callChain,
        callable $assertion,
        $input, // mixed should not be acceptable, but we need to test the case
        ?string $description = null
    ): void {
        $result = self::invokeIteraFn($callChain, $input);
        $assertion($result, $description);
    }

    /**
     * @param Call[] $callChain
     */
    public static function invokeIteraFn(
        iterable $callChain,
        $input
    ) {
        return Pipeline::throughStages(
            $input,
            Itera::apply(
                $callChain,
                fn(Call $call) => IteraFn::{$call->method()}(...$call->args()),
            ),
        );
    }

    public static function assert(
        iterable $callChain,
        callable $assertion,
        $input,
        ?string $description = null,
        ?array $subjects = null
    ): void {
        $subjects ??= [Itera::class, Dash::class, IteraFn::class];
        if (empty($subjects)) {
            throw new LogicException('No test subjects.');
        }
        foreach ($subjects as $class) {
            switch ($class) {
                case Itera::class:
                    $method = 'assertItera';
                    break;
                case Dash::class:
                    $method = 'assertDash';
                    break;
                case IteraFn::class:
                    $method = 'assertIteraFn';
                    break;
                default:
                    $method = null; // or handle the default case as needed
                    break;
            }
            static::{$method}($callChain, $assertion, $input, '[' . $class . '] ' . $description);
        }
    }

    public static function assertThrows(
        iterable $callChain,
        callable $assertion,
        $input,
        ?array $subjects = null
    ): void {
        $subjects ??= [Itera::class, Dash::class, IteraFn::class];
        if (empty($subjects)) {
            throw new LogicException('No test subjects.');
        }
        foreach ($subjects as $class) {
            switch ($class) {
                case Itera::class:
                    $method = 'invokeItera';
                    break;
                case Dash::class:
                    $method = 'invokeDash';
                    break;
                case IteraFn::class:
                    $method = 'invokeIteraFn';
                    break;
                default:
                    $method = null; // or handle the default case as needed
                    break;
            }
            $testCode = fn() => static::{$method}($callChain, $input);
            $invoked = 0;
            $wrapper = function () use ($testCode, &$invoked) {
                $invoked += 1;
                $testCode();
            };
            $assertion($wrapper);
            if ($invoked === 0) {
                throw new LogicException('The assertion did not invoke the test code.');
            }
        }
    }
}
