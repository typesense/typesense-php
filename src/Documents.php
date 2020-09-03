<?php

namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Documents
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Documents implements \ArrayAccess
{

    public const RESOURCE_PATH = 'documents';

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var string
     */
    private $collectionName;

    /**
     * @var \Devloops\Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var array
     */
    private $documents = [];

    /**
     * Documents constructor.
     *
     * @param  \Devloops\Typesence\Lib\Configuration  $config
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
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $document): array
    {
        return $this->apiCall->post($this->endPointPath(''), $document);
    }

    /**
     * @param  array  $documents
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function create_many(array $documents): array
    {
        $documentsStr = [];
        foreach ($documents as $document) {
            $documentsStr[] = json_encode($document);
        }
        $docsImport = implode("\n", $documentsStr);
        return $this->apiCall->post($this->endPointPath('import'), $docsImport);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function export(): array
    {
        $response = $this->apiCall->get($this->endPointPath('export'), [], false);
        return explode("\n", $response);
    }

    /**
     * @param  array  $searchParams
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
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