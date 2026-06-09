<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Synonyms
 *
 * @package \Typesense
 *
 * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->synonymSets` (new Synonym Sets APIs).
 */
class Synonyms implements \ArrayAccess
{
    public const RESOURCE_PATH = 'synonyms';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var array
     */
    private array $synonyms = [];

    /**
     * Synonyms constructor.
     *
     * @param string $collectionName
     * @param ApiCall $apiCall
     */
    public function __construct(string $collectionName, ApiCall $apiCall)
    {
        $this->collectionName = $collectionName;
        $this->apiCall        = $apiCall;
    }

    /**
     * @param string $synonymId
     *
     * @return string
     */
    public function endPointPath(string $synonymId = ''): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            Collections::RESOURCE_PATH,
            encodeURIComponent($this->collectionName),
            static::RESOURCE_PATH,
            encodeURIComponent($synonymId)
        );
    }

    /**
     * Create or update a synonym (legacy v1) on this collection.
     *
     * @example
     * $client->collections['products']->synonyms->upsert('syn-1', ['synonyms' => ['nyc', 'new york']])
     *
     * @see https://typesense.org/docs/29.0/api/synonyms.html
     *
     * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->synonymSets` (new Synonym Sets APIs).
     *
     * @param string $synonymId
     * @param array $config
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(string $synonymId, array $config): array
    {
        return $this->apiCall->put($this->endPointPath($synonymId), $config);
    }

    /**
     * Retrieve all synonyms (legacy v1) on this collection.
     *
     * @example
     * $client->collections['products']->synonyms->retrieve()
     *
     * @see https://typesense.org/docs/29.0/api/synonyms.html
     *
     * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->synonymSets` (new Synonym Sets APIs).
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($synonymId): bool
    {
        return isset($this->synonyms[$synonymId]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($synonymId): Synonym
    {
        if (!isset($this->synonyms[$synonymId])) {
            $this->synonyms[$synonymId] = new Synonym($this->collectionName, $synonymId, $this->apiCall);
        }

        return $this->synonyms[$synonymId];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($synonymId, $value): void
    {
        $this->synonyms[$synonymId] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($synonymId): void
    {
        unset($this->synonyms[$synonymId]);
    }
}
