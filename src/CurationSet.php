<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class CurationSet
 *
 * @package \Typesense
 */
class CurationSet
{
    /**
     * @var string
     */
    private string $curationSetName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    private array $typesenseCurationSetItems = [];

    /**
     * CurationSet constructor.
     *
     * @param string $curationSetName
     * @param ApiCall $apiCall
     */
    public function __construct(string $curationSetName, ApiCall $apiCall)
    {
        $this->curationSetName = $curationSetName;
        $this->apiCall         = $apiCall;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        if (isset($this->{$id})) {
            return $this->{$id};
        }

        if (!isset($this->typesenseCurationSetItems[$id])) {
            $this->typesenseCurationSetItems[$id] = new CurationSetItems($this->curationSetName, $this->apiCall);
        }

        return $this->typesenseCurationSetItems[$id];
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s',
            CurationSets::RESOURCE_PATH,
            encodeURIComponent($this->curationSetName)
        );
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
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

    /**
     * @return CurationSetItems
     */
    public function getItems(): CurationSetItems
    {
        return $this->__get('items');
    }
}
