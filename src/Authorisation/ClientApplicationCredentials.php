<?php

declare(strict_types=1);

namespace Phaze\Common\Authorisation;

use Phaze\Common\Contracts\Authorisation\Authorisation as AuthorisationContract;
use Phaze\Common\Contracts\Authorisation\ClientApplicationCredentials as ClientApplicationCredentialsContract;
use Phaze\Common\Contracts\Authorisation\OAuth2Authoriser as AzureOAuth2AuthoriserContract;

use function Phaze\Common\Utilities\String\scrub;

class ClientApplicationCredentials implements ClientApplicationCredentialsContract
{
    private const PreferredHashAlgorithms = ["murmur3f", "fnv1a64", "crc32",];

    private static ?string $hashAlgorithm = null;

    private string $tenantId;

    private string $clientId;

    private string $clientSecret;

    private ?string $hash = null;

    private AzureOAuth2AuthoriserContract $authoriser;

    public function __construct(string $tenantId, string $clientId, string $clientSecret, ?AzureOAuth2AuthoriserContract $authoriser = null)
    {
        if (null === self::$hashAlgorithm) {
            self::determineHashAlgorithm();
        }

        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->authoriser = $authoriser ?? new OAuth2Authoriser();
    }

    // TODO extract this to a trait?
    private static function determineHashAlgorithm(): void
    {
        $supportedAlgorithms = hash_algos();

        foreach (self::PreferredHashAlgorithms as $algo) {
            if (in_array($algo, $supportedAlgorithms)) {
                self::$hashAlgorithm = $algo;
                return;
            }
        }


        self::$hashAlgorithm = $supportedAlgorithms[0];
    }

    public function __destruct()
    {
        scrub($this->clientSecret);
    }


    public function tenantId(): string
    {
        return $this->tenantId;
    }

    public function withTenantId(string $tenantId): self
    {
        $clone = clone $this;
        $clone->tenantId = $tenantId;
        $clone->hash = null;
        return $this;
    }

    public function clientId(): string
    {
        return $this->clientId;
    }

    public function withClientId(string $clientId): self
    {
        $clone = clone $this;
        $clone->clientId = $clientId;
        $clone->hash = null;
        return $this;
    }


    public function secret(): string
    {
        return $this->clientSecret;
    }


    public function withSecret(string $secret): self
    {
        $clone = clone $this;
        scrub($clone->clientSecret);
        $clone->clientSecret = $secret;
        $clone->hash = null;
        return $clone;
    }

    public function hash(): string
    {
        if (null === $this->hash) {
            $this->hash = hash(self::$hashAlgorithm, "{$this->tenantId()}{$this->clientId()}{$this->secret()}");
        }

        return $this->hash;
    }

    public function authorise(string $resource, string $grantType): AuthorisationContract
    {
        return $this->authoriser->authorise($resource, $grantType, $this);
    }
}
