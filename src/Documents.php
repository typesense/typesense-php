<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

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
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var \Typesense\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $documents = [];

    /**
     * Documents constructor.
     *
     * @param  \Typesense\Lib\Configuration  $config
     * @param  string  $collectionName
     */
    public function __construct(Configuration $config, string $collectionName)
    {
        $this->config         = $config;
        $this->collectionName = $collectionName;
        $this->apiCall        = new ApiCall($config);
    }

    /**
     * @param  string  $action
     *
     * @return string
     */
    private function endPointPath(string $action = ''): string
    {
        return sprintf('%s/%s/%s/%s', Collections::RESOURCE_PATH, $this->collectionName, self::RESOURCE_PATH, $action);
    }

    /**
     * @param  array  $document
     *
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $document): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document);
    }

    /**
     * @param  array  $documents
     *
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException|\JsonException
     */
    public function createMany(array $documents): array
    {
        $res = $this->import(implode("\n", array_map(static fn(array $document) => json_encode($document, JSON_THROW_ON_ERROR), $documents)));
        return array_map(static function ($item) {
            return json_decode($item, true, 512, JSON_THROW_ON_ERROR);
        }, explode("\n", $res));
    }

    /**
     * @param  string  $documents
     *
     * @return string
     * @throws \Typesense\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import(string $documents): string
    {
        return $this->apiCall->post($this->endPointPath('import'), $documents, false);
    }

    /**
     * @return string
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function export(): string
    {
        return $this->apiCall->get($this->endPointPath('export'), [], false);
    }

    /**
     * @param  array  $searchParams
     *
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function search(array $searchParams): array
    {
        return $this->apiCall->get($this->endPointPath('search'), $searchParams);
    }

    /**
     * @param  mixed  $documentId
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
            $this->documents[$documentId] = new Document($this->config, $this->collectionName, $documentId);
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