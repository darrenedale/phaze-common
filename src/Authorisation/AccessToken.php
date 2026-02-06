<?php

namespace Phaze\Common\Authorisation;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Error;
use JsonException;
use TypeError;

use function Phaze\Common\Utilities\String\scrub;

class AccessToken extends AbstractAccessToken
{
    private string $token;
    private string $type;
    private string $resource;
    private int $notBefore;
    private int $expiresOn;

    public function __destruct()
    {
        scrub($this->token);
    }

    public static function fromJson(string $json): self
    {
        $token = new self();

        try {
            [
                "token_type" => $token->type,
                "resource" => $token->resource,
                "access_token" => $token->token,
                "not_before" => $token->notBefore,
                "expires_on" => $token->expiresOn,
            ] = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // TODO throw the correct type of exception
            throw new \RuntimeException("Expected valid JSON Azure AccessToken data structure, found invalid JSON");
        } catch (TypeError $err) {
            throw new \RuntimeException("Invalid data type found in Azure AccessToken data structure");
        } catch (Error $err) {
            // this is the only other error that can occur
            throw new \RuntimeException("Expected JSON property missing from Azure AccessToken data structure");
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

    public function resource(): string
    {
        return $this->resource;
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
