<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Types;

interface Intable
{
    public function toInt(): int;
}
