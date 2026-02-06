<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts;

use Phaze\Common\Contracts\Authorisation\Authorisation;

interface RestClient
{
    public function send(RestCommand $command, ?Authorisation $authorisation = null): mixed;
}
