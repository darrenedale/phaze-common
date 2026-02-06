<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Authorisation;

use Phaze\Common\Exceptions\AuthorisationException;

interface Credentials
{
    public const ServiceBusResource = "https://servicebus.azure.net";

    public const ClientCredentialsGrantType = "client_credentials";

    /** @throws AuthorisationException */
    public function authorise(string $resource, string $grantType): Authorisation;
}
