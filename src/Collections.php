<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Collections
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collections implements \ArrayAccess
{
    public const RESOURCE_PATH = '/collections';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $typesenseCollections = [];

    /**
     * Collections constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param $collectionName
     *
     * @return mixed
     */
    public function __get($collectionName)
    {
        if (isset($this->{$collectionName})) {
            return $this->{$collectionName};
        }
        if (!isset($this->typesenseCollections[$collectionName])) {
            $this->typesenseCollections[$collectionName] = new Collection($collectionName, $this->apiCall);
        }

        return $this->typesenseCollections[$collectionName];
    }

    /**
     * @param array $schema
     * @param array $options
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(array $schema, array $options = []): array
    {
        return $this->apiCall->post(static::RESOURCE_PATH, $schema, true, $options);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(static::RESOURCE_PATH, []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->typesenseCollections[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): Collection
    {
        if (!isset($this->typesenseCollections[$offset])) {
            $this->typesenseCollections[$offset] = new Collection($offset, $this->apiCall);
        }

        return $this->typesenseCollections[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->typesenseCollections[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->typesenseCollections[$offset]);
    }
}
