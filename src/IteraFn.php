<?php

declare(strict_types=1);

namespace Dakujem\Toru;

use Dakujem\Toru\Exceptions\BadMethodCallException;

/**
 * Static factory for partially applied variants of iteration primitives and utilities.
 *
 * The factory methods create partially applied callable equivalents of the `Itera` class methods
 * having the same functionality as their counterparts in the `Itera` class,
 * with the input collection being the only free parameter, fixing the rest.
 * All the returned callables accept a single parameter of iterable type (the input collection).
 * @see Itera
 *
 * Callables returned from the following methods decorate the input iterable, returning a new iterable object (a Generator in most cases).
 * @method static callable chain(iterable ...$more) The chain method effectively appends one or more collections creating a single collection.
 * @method static callable append(iterable ...$more) Alias for `chain`.
 *
 * @method static callable filter(callable $predicate)
 * @method static callable limit(int $limit)
 * @method static callable omit(int $omitted)
 * @method static callable slice(int $offset, int $limit)
 *
 * @method static callable adjust(?callable $values = null, ?callable $keys = null)
 * @method static callable map(callable $values) Alias for `apply`.
 * @method static callable apply(callable $values)
 * @method static callable reindex(callable $keys)
 * @method static callable unfold(callable $mapper)
 * @method static callable valuesOnly()
 * @method static callable keysOnly()
 * @method static callable flip()
 *
 * @method static callable tap(callable $effect)
 * @method static callable each(callable $effect) Alias for `tap`.
 *
 * @method static callable repeat() repeat the whole wrapped collection indefinitely
 * @method static callable loop() yield all elements of the wrapped collection indefinitely
 * @method static callable replicate(int $times) yield all elements of the wrapped collection exactly N times
 *
 * @method static callable toIterator()
 * @method static callable ensureTraversable()
 *
 * Callables returned from the following methods immediately iterate the collection and evaluate all decorators, returning a `mixed` value type.
 * @method static callable toArray() Preserves the original keys. Watch out for collisions.
 * @method static callable toArrayValues() Ignores the original keys.
 * @method static callable reduce(callable $reducer, mixed $initial = null) The reducer has signature `fn(mixed $carry, mixed $value, mixed $key): mixed`.
 * @method static callable search(callable $predicate)
 * @method static callable searchOrFail(callable $predicate)
 * @method static callable firstValue()
 * @method static callable firstKey()
 * @method static callable firstValueOrDefault(mixed $default = null)
 * @method static callable firstKeyOrDefault(mixed $default = null)
 * @method static callable count()
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
final class IteraFn
{
    /**
     * This class may be extended and any of the methods may be implemented directly to change the default behaviour.
     */
    public static function __callStatic(string $name, array $arguments): callable
    {
        if ('append' === $name) {
            $name = 'chain';
        }

        // These methods take and return iterable type, decorating it.
        if (
            'adjust' === $name ||
            'apply' === $name || // == map
            'map' === $name ||
            'reindex' === $name ||
            'filter' === $name ||
            'limit' === $name ||
            'omit' === $name ||
            'slice' === $name ||
            'chain' === $name || // == append
            'tap' === $name || // == each
            'each' === $name ||
            'unfold' === $name ||
            'valuesOnly' === $name ||
            'keysOnly' === $name ||
            'flip' === $name ||

            'loop' === $name ||
            'replicate' === $name ||

            'toIterator' === $name ||
            'ensureTraversable' === $name
        ) {
            return fn(iterable $input): iterable => Itera::{$name}($input, ...$arguments);
        }

        // These methods produce iterable from a mixed type value.
        if (
            'repeat' === $name
        ) {
            return fn($input): iterable => Itera::{$name}($input, ...$arguments);
        }

        // These methods aggregate collections to a mixed type value.
        if (
            'toArray' === $name ||
            'toArrayValues' === $name ||
            'toArrayMerge' === $name ||
            'count' === $name ||
            'search' === $name ||
            'searchOrFail' === $name ||
            'firstValue' === $name ||
            'firstKey' === $name ||
            'firstValueOrDefault' === $name ||
            'firstKeyOrDefault' === $name ||
            'reduce' === $name
        ) {
            // Returning a value, not a collection.
            return fn(iterable $input) => Itera::{$name}($input, ...$arguments);
        }

        $hint = null;
        if (
            'make' === $name ||
            'produce' === $name
        ) {
            $hint = 'The method is not supported in partially applied form.';
        }
        if ('values' === $name) {
            $hint = sprintf('Did you mean `%s::%s`?', self::class, 'valuesOnly');
        }
        if ('keys' === $name) {
            $hint = sprintf('Did you mean `%s::%s`?', self::class, 'keysOnly');
        }
        if ('find' === $name || 'findOrDefault' === $name) {
            $hint = sprintf('Did you mean `%s::%s`?', self::class, 'search');
        }
        if ('findOrFail' === $name) {
            $hint = sprintf('Did you mean `%s::%s`?', self::class, 'searchOrFail');
        }
        throw new BadMethodCallException(
            sprintf('Invalid call to `%s::%s`.', self::class, $name) .
            (null !== $hint ? ' ' . $hint : '')
        );
    }
}
