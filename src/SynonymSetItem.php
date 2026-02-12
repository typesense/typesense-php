<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class SynonymSetItem
 *
 * @package \Typesense
 */
class SynonymSetItem
{
    /**
     * @var string
     */
    private string $synonymSetName;

    /**
     * @var string
     */
    private string $itemId;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * SynonymSetItem constructor.
     *
     * @param string $synonymSetName
     * @param string $itemId
     * @param ApiCall $apiCall
     */
    public function __construct(string $synonymSetName, string $itemId, ApiCall $apiCall)
    {
        $this->synonymSetName = $synonymSetName;
        $this->itemId         = $itemId;
        $this->apiCall        = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s/items/%s',
            SynonymSets::RESOURCE_PATH,
            encodeURIComponent($this->synonymSetName),
            encodeURIComponent($this->itemId)
        );
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(array $params): array
    {
        return $this->apiCall->put($this->endPointPath(), $params);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }
}
