<?php

declare(strict_types=1);

namespace Phaze\Common\Utilities\Casts;

use InvalidArgumentException;
use Phaze\Common\Contracts\Types\Boolable as BoolableContract;
use Phaze\Common\Contracts\Types\Intable as IntableContract;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Stringable;

use function Phaze\Common\Utilities\String\parseBoolean;
use function Phaze\Common\Utilities\String\parseInt;

/** Turn a value into a string suitable for REST request header or URI */
function toString(mixed $value): string
{
    return match (true) {
        is_string($value) => $value,
        is_int($value), is_float($value), $value instanceof Stringable => (string) $value,
        is_bool($value) => $value ? "true" : "false",
        $value instanceof StringableContract => $value->toString(),
        default => throw new InvalidArgumentException("Expecting a value that can be converted to string, found " . (is_object($value) ? $value::class : gettype($value))),
    };
}

function toInt(mixed $value): int
{
    return match(true) {
        is_int($value) => $value,
        is_float($value) => (int) $value,
        is_bool($value) => ($value ? -1 : 0),
        is_null($value) => 0,
        is_string($value) => parseInt($value),
        $value instanceof IntableContract => $value->toInt(),
        default => throw new InvalidArgumentException("Expecting a value that can be converted to int, found " . (is_object($value) ? $value::class : gettype($value))),
    };
}

function toBool(mixed $value): bool
{
    return match(true) {
        is_bool($value) => $value,
        is_int($value), is_float($value) => (bool) $value,
        is_null($value) => false,
        is_string($value) => parseBoolean($value),
        $value instanceof BoolableContract => $value->toBoolean(),
        default => throw new InvalidArgumentException("Expecting a value that can be converted to bool, found " . (is_object($value) ? $value::class : gettype($value))),
    };
}
