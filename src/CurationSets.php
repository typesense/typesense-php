<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class CurationSets
 *
 * @package \Typesense
 */
class CurationSets implements \ArrayAccess
{
    public const RESOURCE_PATH = '/curation_sets';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $curationSets = [];

    /**
     * CurationSets constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param string $curationSetName
     * @param array $config
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(string $curationSetName, array $config): array
    {
        return $this->apiCall->put(sprintf('%s/%s', static::RESOURCE_PATH, encodeURIComponent($curationSetName)), $config);
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
    public function offsetExists($curationSetName): bool
    {
        return isset($this->curationSets[$curationSetName]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($curationSetName): CurationSet
    {
        if (!isset($this->curationSets[$curationSetName])) {
            $this->curationSets[$curationSetName] = new CurationSet($curationSetName, $this->apiCall);
        }

        return $this->curationSets[$curationSetName];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($curationSetName, $value): void
    {
        $this->curationSets[$curationSetName] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($curationSetName): void
    {
        unset($this->curationSets[$curationSetName]);
    }
}
