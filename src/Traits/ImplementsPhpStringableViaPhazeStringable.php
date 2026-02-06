<?php

declare(strict_types=1);

namespace Phaze\Common\Traits;

trait ImplementsPhpStringableViaPhazeStringable
{
    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }
}
