<?php

declare(strict_types=1);

namespace Typesense;

use Psr\Http\Client\ClientInterface;
use Typesense\Requests\Collection;
use Typesense\Requests\Document;

/**
 * @phpstan-type TypesenseConfiguration array{
 *     url: string,
 *     apiKey: string,
 *     http?: ClientInterface|null,
 * }
 */
class Typesense
{
    public Http $http;

    public Collection $collection;

    public Document $document;

    /**
     * @param  TypesenseConfiguration  $config
     */
    public function __construct(array $config)
    {
        $this->http = new Http($config);

        $this->collection = new Collection($this->http);

        $this->document = new Document($this->http);
    }

    public function setUrl(string $url): static
    {
        $this->http->config['url'] = $url;

        return $this;
    }

    public function setApiKey(string $key): static
    {
        $this->http->config['apiKey'] = $key;

        return $this;
    }

    public function setHttp(ClientInterface $http): static
    {
        $this->http->config['http'] = $http;

        return $this;
    }
}
