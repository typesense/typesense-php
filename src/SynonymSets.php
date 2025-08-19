<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class SynonymSets
 *
 * @package \Typesense
 */
class SynonymSets implements \ArrayAccess
{
    public const RESOURCE_PATH = '/synonym_sets';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $synonymSets = [];

    /**
     * SynonymSets constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param string $synonymSetName
     * @param array $config
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(string $synonymSetName, array $config): array
    {
        return $this->apiCall->put(sprintf('%s/%s', static::RESOURCE_PATH, encodeURIComponent($synonymSetName)), $config);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(static::RESOURCE_PATH, []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($synonymSetName): bool
    {
        return isset($this->synonymSets[$synonymSetName]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($synonymSetName): SynonymSet
    {
        if (!isset($this->synonymSets[$synonymSetName])) {
            $this->synonymSets[$synonymSetName] = new SynonymSet($synonymSetName, $this->apiCall);
        }

        return $this->synonymSets[$synonymSetName];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($synonymSetName, $value): void
    {
        $this->synonymSets[$synonymSetName] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($synonymSetName): void
    {
        unset($this->synonymSets[$synonymSetName]);
    }
} 