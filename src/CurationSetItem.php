<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class CurationSetItem
 *
 * @package \Typesense
 */
class CurationSetItem
{
    /**
     * @var string
     */
    private string $curationSetName;

    /**
     * @var string
     */
    private string $itemId;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * CurationSetItem constructor.
     *
     * @param string $curationSetName
     * @param string $itemId
     * @param ApiCall $apiCall
     */
    public function __construct(string $curationSetName, string $itemId, ApiCall $apiCall)
    {
        $this->curationSetName = $curationSetName;
        $this->itemId          = $itemId;
        $this->apiCall         = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s/items/%s',
            CurationSets::RESOURCE_PATH,
            encodeURIComponent($this->curationSetName),
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
