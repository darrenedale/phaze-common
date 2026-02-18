<?php

namespace Phaze\Common\Authorisation;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonException;
use Phaze\Common\Exceptions\PhazeException;
use TypeError;

use function Phaze\Common\Utilities\String\scrub;

class AccessToken extends AbstractAccessToken
{
    private string $token = "";

    private string $type = "";

    private string $resource = "";

    private int $notBefore = 0;

    private int $expiresOn = 0;

    public function __destruct()
    {
        scrub($this->token);
    }

    public static function fromJson(string $json): self
    {
        $token = new self();

        try {
            $tokenData = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $error) {
            throw new PhazeException("Expected valid JSON Azure AccessToken data structure, found invalid JSON", previous: $error);
        }

        foreach(["token_type", "resource", "access_token", "not_before", "expires_on"] as $property) {
            if (!array_key_exists($property, $tokenData)) {
                throw new PhazeException("Expected JSON property missing from Azure AccessToken data structure");
            }
        }

        try {
            [
                "token_type" => $token->type,
                "resource" => $token->resource,
                "access_token" => $token->token,
                "not_before" => $token->notBefore,
                "expires_on" => $token->expiresOn,
            ] = $tokenData;
        } catch (TypeError $error) {
            throw new PhazeException("Invalid data type found in Azure AccessToken data structure", previous: $error);
        }

        return $token;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function resource(): string
    {
        return $this->resource;
    }

    public function notBefore(): int
    {
        return $this->notBefore;
    }

    public function notBeforeDateTime(): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat("U", $this->notBefore, new DateTimeZone("Z"));
    }

    public function expiresOn(): int
    {
        return $this->expiresOn;
    }

    public function expiresOnDateTime(): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat("U", $this->expiresOn, new DateTimeZone("Z"));
    }

    public function __toString(): string
    {
        return $this->token;
    }

    public function headers(): array
    {
        return ["Authorization" => "Bearer {$this->token()}",];
    }
}
