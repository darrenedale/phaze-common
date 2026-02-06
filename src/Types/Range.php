<?php

declare(strict_types=1);

namespace Phaze\Common\Types;

use LogicException;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable as Stringable;
use Throwable;

use function Phaze\Common\Utilities\String\isValidRfc9110Token;

class Range implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    private string $unit;

    /**
     * An array of tuples of [first, last]
     *
     * @var array<array{0:int|null,1:int|null}>
     */
    private array $ranges;

    public function __construct(string $unit, ?int $first = null, ?int $last = null)
    {
        assert (null !== $first || null !== $last, new LogicException("Expected first or last position in range (or both), neither found"));
        self::checkUnit($unit);

        if (null !== $first && 0 > $first) {
            throw new InvalidValueException("Expected valid first position, found {$first}");
        }

        if (null !== $last && 0 > $last) {
            throw new InvalidValueException("Expected valid last position, found {$last}");
        }

        $this->unit = $unit;
        $this->ranges[] = [$first, $last,];
    }

    final protected static function checkUnit(string $unit): void
    {
        if (!isValidRfc9110Token($unit)) {
            throw new InvalidValueException("Expected valid Range header unit, found \"{$unit}\"");
        }
    }

    final protected static function checkBounds(?int $first, ?int $last): void
    {
        if (is_int($first) && is_int($last) && $first > $last) {
            throw new InvalidValueException("Expected range with lower first element than last element, found {$first}-{$last}");
        }
    }

    final public static function parse(string $range): self
    {
        // pattern to match ?int-?int with any surrounding whitespace, requiring at least one of the ?ints to be an int
        static $rangeSpec = "(?:(\\d+)\\s*-\\s*(\\d*)|(\\d*)\\s*-\\s*(\\d+))";

        try {
            [$unit, $ranges] = explode("=", $range, 2);
        } catch (Throwable) {
            throw new InvalidValueException("Expected valid Range header value, found {$range}");
        }

        self::checkUnit($unit);

        // a valid range spec followed by 0 or instances of a comma followed by a valid range spec
        if (!preg_match("/^\\s*{$rangeSpec}(?:\\s*,\\s*{$rangeSpec})*\\s*\$/", $ranges, $rangeMatches)) {
            throw new InvalidValueException("Expected valid Range header range specifications, found {$ranges}");
        }

        $ranges = [];

        while (0 < count($rangeMatches)) {
            $first = array_shift($rangeMatches);
            $last = array_shift($rangeMatches);

            $first = ("" === $first ? null : (int) $first);
            $last = ("" === $last ? null : (int) $last);

            // regex ensures they can't both be null
            if (is_int($first) && is_int($last) && $first > $last) {
                throw new InvalidValueException("Expected range with lower first element than last element, found {$first}-{$last}");
            }

            $ranges[] = [$first, $last,];
        }

        $range = new self($unit, 0, 1);
        $range->ranges = $ranges;
        return $range;
    }

    public function unit(): string
    {
        return $this->unit;
    }

    public function withUnit(string $unit): self
    {
        self::checkUnit($unit);
        $clone = clone $this;
        $clone->unit = $unit;
        return $clone;
    }

    /** @return array<array{0:int|null,1:int|null}> */
    public function ranges(): array
    {
        return $this->ranges;
    }

    public function withRange(?int $first, ?int $last): self
    {
        if (null === $first && null === $last) {
            throw new InvalidValueException("Expected valid range bounds, found none");
        }

        self::checkBounds($first, $last);

        $clone = clone $this;
        $clone->ranges[] = [$first, $last,];
        return $clone;
    }

    public function toString(): string
    {
        $ranges = array_map(fn (array $rangeSpec): string => "{$rangeSpec[0]}-{$rangeSpec[1]}", $this->ranges());
        return "{$this->unit}=" . implode(",", $ranges);
    }
}
