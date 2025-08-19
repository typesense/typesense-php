<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class SynonymSet
 *
 * @package \Typesense
 */
class SynonymSet
{
    /**
     * @var string
     */
    private string $synonymSetName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * SynonymSet constructor.
     *
     * @param string $synonymSetName
     * @param ApiCall $apiCall
     */
    public function __construct(string $synonymSetName, ApiCall $apiCall)
    {
        $this->synonymSetName = $synonymSetName;
        $this->apiCall        = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s',
            SynonymSets::RESOURCE_PATH,
            encodeURIComponent($this->synonymSetName)
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
} 