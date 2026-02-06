<?php

declare(strict_types=1);

namespace Phaze\Common\Utilities\Response;

use Phaze\Common\Exceptions\ResponseException;
use Psr\Http\Message\ResponseInterface;

function readSingleHeader(ResponseInterface $response, string $name): string
{
    $headers = $response->getHeader($name);

    return match (count($headers)) {
        1 => $headers[0],
        0 => throw new ResponseException("Expecting response header \"{$name}\", none found"),
        default => throw new ResponseException("Expecting single response header \"{$name}\", found " . count($headers)),
    };
}
