<?php

declare(strict_types=1);

namespace Phaze\Common\Utilities\Iterable;

/**
 * @template T
 * @template U
 * @param iterable<T> $iterable
 * @param callable(T):U|callable(T,string|int):U $fn
 * @return iterable<U>
 */
function map(iterable $iterable, callable $fn): iterable
{
    foreach ($iterable as $key => $value) {
        yield $key => $fn($value, $key);
    }
}

/**
 * Verify that all members of an iterable satisfy a predicate.
 *
 * The predicate is provided with the value and keh from the iterable. If you don't care about the keys, the predicate
 * can just accept a single argument, the value.
 *
 * The provided iterable will be at least partially traversed, fully traversed if all members satisfy the predicate.
 *
 * @param iterable $iterable The iterable to verify.
 * @param callable $predicate The predicate.
 *
 * @return bool `true` if all members of the iterable satisfy the predicate, `false` otherwise.
 */
function all(iterable $iterable, callable $predicate): bool
{
    foreach ($iterable as $key => $value) {
        if (!$predicate($value, $key)) {
            return false;
        }
    }

    return true;
}

/**
 * @template T of int|string
 * @template U
 * @param iterable<T,U> $iterable
 * @return array<T,U>
 */
function toArray(iterable $iterable): array
{
    if (is_array($iterable)) {
        return $iterable;
    }

    return iterator_to_array($iterable);
}
