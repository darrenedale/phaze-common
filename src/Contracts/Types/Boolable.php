<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Types;

interface Boolable
{
    public function toBoolean(): bool;
}
