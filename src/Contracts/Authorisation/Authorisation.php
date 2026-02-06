<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Authorisation;

interface Authorisation
{
    public function hasExpired(): bool;

    /** @return array<string,string> */
    public function headers(): array;
}
