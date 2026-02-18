<?php

namespace Phaze\Common\Authorisation;

use DateTimeInterface;
use Phaze\Common\Contracts\Authorisation\Authorisation as AuthorisationContract;

abstract class AbstractAccessToken implements AuthorisationContract
{
    abstract public function token(): string;

    abstract public function type(): string;

    abstract public function notBefore(): int;

    abstract public function notBeforeDateTime(): DateTimeInterface;

    abstract public function expiresOn(): int;

    abstract public function expiresOnDateTime(): DateTimeInterface;

    abstract public function resource(): string;

    public function hasExpired(): bool
    {
        $time = time();
        return $this->notBefore() > $time || $this->expiresOn() <= $time;
    }
}
