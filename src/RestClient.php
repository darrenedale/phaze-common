<?php

declare(strict_types=1);

namespace Phaze\Common;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request;
use Phaze\Common\Contracts\Authorisation\Authorisation as AuthorisationContract;
use Phaze\Common\Contracts\RestClient as RestClientContract;
use Phaze\Common\Contracts\RestCommand as RestCommandContract;
use Phaze\Common\Exceptions\AuthorisationException;
use Phaze\Common\Exceptions\PhazeException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class RestClient implements RestClientContract
{
    private ClientInterface $httpClient;

    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new HttpClient();
    }

    public function httpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function withHttpClient(ClientInterface $httpClient): self
    {
        $clone = clone $this;
        $clone->httpClient = $httpClient;
        return $clone;
    }

    public function send(RestCommandContract $command, ?AuthorisationContract $authorisation = null): mixed
    {
        try {
            $headers = array_merge($command->headers(), $authorisation?->headers() ?? []);
            $response = $this->httpClient()->sendRequest(new Request($command->method(), $command->uri(), $headers, $command->body()));
        } catch (ClientExceptionInterface $err) {
            // TODO use the correct exception type
            throw new PhazeException("Network error communicating with Azure REST API endpoint {$command->uri()}: {$err->getMessage()}", previous: $err);
        }

        if (401 === $response->getStatusCode()) {
            throw new AuthorisationException("Azure REST API returned a 401 Not Authorised response: {$response->getReasonPhrase()}");
        }

        return $command->parseResponse($response);
    }
}
