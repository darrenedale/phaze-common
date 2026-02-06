<?php

declare(strict_types=1);

namespace Phaze\Common\Types;

use LogicException;
use Phaze\Common\Exceptions\InvalidValueException;
use Stringable as StringableContract;

class Guid implements StringableContract
{
    public const BraceEncloser = "{";

    public const ParenthesisEncloser = "(";

    public const NoEncloser = "";

    private const Alphabet = "abcdefABCDEF0123456789";

    /** @var string The GUID in the form in which it was originally constructued. */
    private string $guid;

    /** @var string The base 32-digit hex GUID. */
    private string $baseGuid;

    public function __construct(string $guid)
    {
        $encloser = $guid[0] ?? null;

        if (str_contains("({", $encloser)) {
            if (substr($guid, -1) !== $encloser) {
                throw new InvalidValueException("Expected matching GUID enclosers, found {$encloser} and " . substr($guid, -1));
            }

            $guid = substr($guid, 1, -1);
        } elseif (null === $encloser) {
            throw new InvalidValueException("Expected valid GUID, found empty string");
        }

        $originalGuid = $guid;

        if (36 === strlen($guid)) {
            if ("-" !== $guid[8] || "-" !== $guid[13] || "-" !== $guid[18] || "-" !== $guid[23]) {
                throw new InvalidValueException("Expected GUID with 8, 4, 4, 4 and 12 hex digit groups, found {$originalGuid}");
            }

            $guid = str_replace("-", "", $guid);
        }

        if (32 !== strlen($guid) || strlen($guid) !== strspn($guid, self::Alphabet)) {
            throw new InvalidValueException("Expected GUID with 32 hex digits, found {$originalGuid}");
        }

        $this->guid = $originalGuid;
        $this->baseGuid = $guid;
    }

    /** Format the GUID in a specific (valid) way. */
    public function formatted(bool $delimited = false, string $encloser = self::NoEncloser): string
    {
        assert ($encloser === self::NoEncloser || $encloser === self::BraceEncloser || $encloser === self::ParenthesisEncloser, new LogicException("Expected valid encloser, found {$encloser}"));
        $guid = $this->baseGuid;

        if ($delimited) {
            $guid = substr($guid, 0, 8)
                . "-" . substr($guid, 8, 4)
                . "-" . substr($guid, 12, 4)
                . "-" . substr($guid, 16, 4)
                . "-" . substr($guid, 20);
        }

        return "{$encloser}{$guid}" . match ($encloser) {
            self::NoEncloser => "",
            self::BraceEncloser => "}",
            self::ParenthesisEncloser => ")",
        };
    }

    /** The GUID is always returned in its original form from which it was constructed. */
    public function guid(): string
    {
        return $this->guid;
    }

    public function __toString(): string
    {
        return $this->guid();
    }
}
