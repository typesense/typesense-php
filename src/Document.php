<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Document
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Document
{

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var string
     */
    private string $documentId;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Document constructor.
     *
     * @param string $collectionName
     * @param string $documentId
     * @param ApiCall $apiCall
     */
    public function __construct(string $collectionName, string $documentId, ApiCall $apiCall)
    {
        $this->collectionName = $collectionName;
        $this->documentId     = $documentId;
        $this->apiCall        = $apiCall;
    }

    /**
     * @return string
     */
    private function endpointPath(): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            Collections::RESOURCE_PATH,
            $this->collectionName,
            Documents::RESOURCE_PATH,
            $this->documentId
        );
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * @param array $partialDocument
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function update(array $partialDocument): array
    {
        return $this->apiCall->patch($this->endpointPath(), $partialDocument);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }
}
