<?php

declare(strict_types=1);

namespace Phaze\Common\Contracts\Authorisation;

use Phaze\Common\Exceptions\AuthorisationException;

interface OAuth2Authoriser
{
    public const ClientCredentialsGrantType = "client_credentials";

    public const ServiceBusResource = "https://servicebus.azure.net";

    public const StorageAccountResource = "https://storage.azure.com";

    /** @throws AuthorisationException on error */
    public function authorise(string $resource, string $grantType, ClientApplicationCredentials $credentials): Authorisation;
}
