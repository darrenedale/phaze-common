<?php

declare(strict_types=1);

namespace Phaze\Common\Traits\RestCommands;

trait HasNoHeaders
{
    public function headers(): array
    {
        return [];
    }
}
