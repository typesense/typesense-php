<?php

declare(strict_types=1);

namespace Typesense;

use Psr\Http\Client\ClientInterface;
use Typesense\Requests\Alias;
use Typesense\Requests\Analytic;
use Typesense\Requests\Cluster;
use Typesense\Requests\Collection;
use Typesense\Requests\Curation;
use Typesense\Requests\Document;
use Typesense\Requests\Key;
use Typesense\Requests\Synonym;

/**
 * @phpstan-type TypesenseConfiguration array{
 *     url: string,
 *     apiKey: string,
 *     http?: ClientInterface|null,
 * }
 */
class Typesense
{
    public readonly Http $http;

    public readonly Collection $collection;

    public readonly Document $document;

    public readonly Analytic $analytic;

    public readonly Key $key;

    public readonly Curation $curation;

    public readonly Alias $alias;

    public readonly Synonym $synonym;

    public readonly Cluster $cluster;

    /**
     * @param  TypesenseConfiguration  $config
     */
    public function __construct(array $config)
    {
        $this->http = new Http($config);

        $this->collection = new Collection($this->http);

        $this->document = new Document($this->http);

        $this->analytic = new Analytic($this->http);

        $this->key = new Key($this->http);

        $this->curation = new Curation($this->http);

        $this->alias = new Alias($this->http);

        $this->synonym = new Synonym($this->http);

        $this->cluster = new Cluster($this->http);
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
