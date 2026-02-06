<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Types;

interface Arrayable
{
    public function toArray(): array;
}
