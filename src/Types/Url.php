<?php

declare(strict_types=1);

namespace Phaze\Types;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class Url implements Stringable, StringableContract
{
    use ImplementsPhpStringableViaPhazeStringable;

    private string $url;

    public function __construct(string $url)
    {
        if (null === ($url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED | FILTER_NULL_ON_FAILURE))) {
            throw new InvalidValueException("Expected valid URL string, found \"{$url}\"");
        }

        $this->url = $url;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function toString(): string
    {
        return $this->url();
    }
}
