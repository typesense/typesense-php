<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
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
        $this->apiCall        = $apiCall;
    }

    /**
     * @param string $action
     *
     * @return string
     */
    private function endPointPath(string $action = ''): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            Collections::RESOURCE_PATH,
            encodeURIComponent($this->collectionName),
            static::RESOURCE_PATH,
            $action
        );
    }

    /**
     * Index a single document. The document must conform to the schema of the collection.
     *
     * @example
     * $client->collections['products']->documents->create(['id' => '1', 'title' => 'Hat'])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#index-a-single-document
     *
     * @param array $document
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(array $document, array $options = []): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document, true, $options);
    }

    /**
     * Upsert a single document. Creates the document if it does not exist, otherwise updates it.
     *
     * @example
     * $client->collections['products']->documents->upsert(['id' => '1', 'title' => 'Hat'])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#upsert
     *
     * @param array $document
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(array $document, array $options = []): array
    {
        return $this->apiCall->post(
            $this->endPointPath(''),
            $document,
            true,
            array_merge($options, ['action' => 'upsert'])
        );
    }

    /**
     * Update documents matching a `filter_by` condition, or update a single document with
     * `action: "update"` semantics.
     *
     * @example
     * $client->collections['products']->documents->update(['in_stock' => true], ['filter_by' => 'category:=hats'])
     * @example
     * $client->collections['products']->documents->update(['id' => '1', 'title' => 'Hat'])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#update-documents-with-conditional-query
     *
     * @param array $document
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function update(array $document, array $options = []): array
    {
        if (empty($options['filter_by'])) {
            return $this->apiCall->post(
                $this->endPointPath(''),
                $document,
                true,
                array_merge($options, ['action' => 'update'])
            );
        }

        return $this->apiCall->patch(
            $this->endPointPath(''),
            $document,
            true,
            $options
        );
    }

    /**
     * @param array $documents
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException|\JsonException
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
     * Import documents into a collection. Accepts a JSONL string or an array of document objects.
     *
     * @example
     * $client->collections['products']->documents->import([['id' => '1', 'title' => 'Hat'], ['id' => '2', 'title' => 'Shirt']])
     * @example
     * $client->collections['products']->documents->import($jsonlString)
     *
     * @see https://typesense.org/docs/latest/api/documents.html#index-multiple-documents
     *
     * @param string|array $documents
     * @param array $options
     *
     * @return string|array
     * @throws TypesenseClientError
     * @throws \JsonException|HttpClientException
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
     * Export all documents in a collection as a JSONL string.
     *
     * @example
     * $client->collections['products']->documents->export()
     *
     * @see https://typesense.org/docs/latest/api/documents.html#export-documents
     *
     * @param array $queryParams
     *
     * @return string
     * @throws TypesenseClientError|HttpClientException
     */
    public function export(array $queryParams = []): string
    {
        return $this->apiCall->get($this->endPointPath('export'), $queryParams, false);
    }

    /**
     * Delete a bunch of documents that match a specific filter condition,
     * or truncate the collection.
     *
     * @example
     * $client->collections['products']->documents->delete(['filter_by' => 'in_stock:=false'])
     * @example
     * $client->collections['products']->documents->delete(['truncate' => true])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#delete-by-query
     *
     * @param array $queryParams
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(array $queryParams = []): array
    {
        return $this->apiCall->delete($this->endPointPath(), true, $queryParams);
    }

    /**
     * Search for documents in this collection.
     *
     * @example
     * $client->collections['products']->documents->search(['q' => '*'])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#search
     *
     * @param array $searchParams
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
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
