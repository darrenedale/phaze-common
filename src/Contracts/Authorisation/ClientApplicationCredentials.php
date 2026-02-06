<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Authorisation;

interface ClientApplicationCredentials extends Credentials
{
    public function tenantId(): string;

    public function clientId(): string;

    public function secret(): string;
}
