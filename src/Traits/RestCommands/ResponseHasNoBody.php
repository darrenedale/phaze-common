<?php

declare(strict_types=1);

namespace Phaze\Common\Traits\RestCommands;

use Phaze\Common\Exceptions\PhazeException;
use Psr\Http\Message\ResponseInterface;

trait ResponseHasNoBody
{
    abstract public function uri(): string;

    private function createErrorException(ResponseInterface $response): PhazeException
    {
        return new PhazeException("Error received from REST API for request {$this->uri()}: {$response->getReasonPhrase()}");
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        if (200 <= $response->getStatusCode() && 300 > $response->getStatusCode()) {
            return null;
        }

        throw self::createErrorException($response);
    }
}
