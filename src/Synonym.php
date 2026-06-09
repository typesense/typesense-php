<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class synonym
 *
 * @package \Typesense
 *
 * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->synonymSets` (new Synonym Sets APIs).
 */
class Synonym
{
    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var string
     */
    private string $synonymId;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * synonym constructor.
     *
     * @param string $collectionName
     * @param string $synonymId
     * @param ApiCall $apiCall
     */
    public function __construct(string $collectionName, string $synonymId, ApiCall $apiCall)
    {
        $this->collectionName = $collectionName;
        $this->synonymId      = $synonymId;
        $this->apiCall        = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            Collections::RESOURCE_PATH,
            encodeURIComponent($this->collectionName),
            synonyms::RESOURCE_PATH,
            encodeURIComponent($this->synonymId)
        );
    }

    /**
     * Retrieve a synonym (legacy v1) by ID on this collection.
     *
     * @example
     * $client->collections['products']->synonyms['syn-1']->retrieve()
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
     * Delete a synonym (legacy v1) by ID on this collection.
     *
     * @example
     * $client->collections['products']->synonyms['syn-1']->delete()
     *
     * @see https://typesense.org/docs/29.0/api/synonyms.html
     *
     * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->synonymSets` (new Synonym Sets APIs).
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }
}
