<?php

declare(strict_types=1);

namespace Phaze\Common\Traits\RestCommands;

trait HasNoBody
{
    public function body(): string
    {
        return "";
    }
}
