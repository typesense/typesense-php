<?php

declare(strict_types=1);

namespace Typesense;

use Http\Discovery\Psr18Client;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-import-type TypesenseConfiguration from Typesense
 */
class Http
{
    public Psr18Client $client;

    /**
     * @param  TypesenseConfiguration  $config
     */
    public function __construct(
        public array $config,
    ) {
        $this->client = new Psr18Client($config['http'] ?? null);
    }

    /**
     * @param  'GET'|'HEAD'|'POST'|'PATCH'|'PUT'|'DELETE'  $method
     */
    public function request(string $method, string $path, string $body = ''): ResponseInterface
    {
        $request = $this->client
            ->createRequest($method, $this->uri($path))
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-TYPESENSE-API-KEY', $this->config['apiKey']);

        if (! empty($body)) {
            $request = $request->withBody(
                $this->client->createStream($body),
            );
        }

        return $this->client->sendRequest($request);
    }

    /**
     * Form a complete request URL.
     */
    public function uri(string $path): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->config['url'], '/'),
            ltrim($path, '/'),
        );
    }
}
