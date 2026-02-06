<?php

declare(strict_types=1);

namespace Phaze\Common\Utilities\String;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Phaze\Common\Constants;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Stringable;
use Throwable;

function parseBoolean(string $bool): bool
{
    return match(strtolower($bool)) {
        "false" => false,
        "true" => true,
        default => throw new InvalidArgumentException("Expected boolean string, found {$bool}"),
    };
}

function parseDateTime(string $dateTime, string $format = Constants::DateTimeFormat): DateTimeInterface
{
    try {
        return DateTimeImmutable::createFromFormat($format, $dateTime, new DateTimeZone("UTC"));
    } catch (Throwable $err) {
        throw new InvalidArgumentException(("Expected date-time string formatted as \"{$format}\", found {$dateTime}: {$err->getMessage()}"));
    }
}

function parseInt(string $value): int
{
    $int = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

    if (null === $int) {
        throw new InvalidArgumentException("Expected int string, found \"{$value}\"");
    }

    return $int;
}

function isValidRfc9110Token(string $token): bool
{
    static $alphabet = "!#\$%&'*+-.^_`|~0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    return strlen($token) === strspn($token, $alphabet);
}

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

function scrub(string & $string): void
{
    for ($idx = strlen($string) - 1; $idx >= 0; --$idx) {
        $string[$idx] = chr(mt_rand(0, 255));
    }
}

/**
 * If a string is quoted with "" or '' it will be unquoted and returned.
 *
 * The string is in no way unescaped - the content between the quotes is returned unmodified.
 */
function unquote(string $string): string
{
    // can't be quoted if it hasn't enough characters for two quotes
    if (2 > strlen($string)) {
        return $string;
    }

    if ($string[0] !== $string[-1]) {
        return $string;
    }

    return match ($string[0]) {
        "\"", "'" => substr($string, 1, -1),
        default => $string,
    };
}
