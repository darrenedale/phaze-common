<?php

declare(strict_types=1);

namespace Phaze\Common\Types;

use Phaze\Common\Contracts\Types\Intable;
use Phaze\Common\Contracts\Types\Stringable;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;

class UnsignedInteger implements Intable, Stringable
{
    use ImplementsPhpStringableViaPhazeStringable;

    private int $value;

    public function __construct(int $value)
    {
        if (0 > $value) {
            throw new InvalidValueException("Expected int >= 0, found {$value}");
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return $this->value();
    }

    public function toString(): string
    {
        return (string) $this->value();
    }
}
