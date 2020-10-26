<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Documents
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Documents implements \ArrayAccess
{

    public const RESOURCE_PATH = 'documents';

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $documents = [];

    /**
     * Documents constructor.
     *
     * @param string $collectionName
     * @param ApiCall $apiCall
     */
    public function __construct(string $collectionName, ApiCall $apiCall)
    {
        $this->collectionName = $collectionName;
        $this->apiCall = $apiCall;
    }

    /**
     * @param string $action
     *
     * @return string
     */
    private function endPointPath(string $action = ''): string
    {
        return sprintf('%s/%s/%s/%s', Collections::RESOURCE_PATH, $this->collectionName, self::RESOURCE_PATH, $action);
    }

    /**
     * @param array $document
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function create(array $document): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document, true);
    }

    /**
     * @param array $document
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function upsert(array $document): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document, true, ['action' => 'upsert']);
    }

    /**
     * @param array $document
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function update(array $document): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document, true, ['action' => 'update']);
    }

    /**
     * @param array $documents
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException|\JsonException
     */
    public function createMany(array $documents, array $options = []): array
    {
        $this->apiCall->getLogger()->warning(
            "createMany is deprecated and will be removed in a future version. " .
            "Use import instead, which now takes both an array of documents or a JSONL string of documents"
        );
        return $this->import($documents, $options);
    }

    /**
     * @param string|array $documents
     * @param array $options
     *
     * @return string|array
     * @throws TypesenseClientError
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function import($documents, array $options = [])
    {
        if (is_array($documents)) {
            $documentsInJSONLFormat = implode(
                "\n",
                array_map(
                    static fn(array $document) => json_encode($document, JSON_THROW_ON_ERROR),
                    $documents
                )
            );
        } else {
            $documentsInJSONLFormat = $documents;
        }
        $resultsInJSONLFormat = $this->apiCall->post(
            $this->endPointPath('import'),
            $documentsInJSONLFormat,
            false,
            $options
        );

        if (is_array($documents)) {
            return array_map(static function ($item) {
                return json_decode($item, true, 512, JSON_THROW_ON_ERROR);
            }, explode("\n", $resultsInJSONLFormat));
        } else {
            return $resultsInJSONLFormat;
        }
    }

    /**
     * @return string
     * @throws TypesenseClientError|GuzzleException
     */
    public function export(): string
    {
        return $this->apiCall->get($this->endPointPath('export'), [], false);
    }

    /**
     * @param array $searchParams
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function search(array $searchParams): array
    {
        return $this->apiCall->get($this->endPointPath('search'), $searchParams);
    }

    /**
     * @param mixed $documentId
     *
     * @return bool
     */
    public function offsetExists($documentId): bool
    {
        return isset($this->documents[$documentId]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($documentId): Document
    {
        if (!isset($this->documents[$documentId])) {
            $this->documents[$documentId] = new Document($this->collectionName, $documentId, $this->apiCall);
        }

        return $this->documents[$documentId];
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($documentId): void
    {
        if (isset($this->documents[$documentId])) {
            unset($this->documents[$documentId]);
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->documents[$offset] = $value;
    }
}
