<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
use Typesense\Exceptions\TypesenseClientError;
use Typesense\Lib\Configuration;

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
    private array $collections = [];

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
        if (!isset($this->collections[$collectionName])) {
            $this->collections[$collectionName] = new Collection($collectionName, $this->apiCall);
        }

        return $this->collections[$collectionName];
    }

    /**
     * @param array $schema
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function create(array $schema): array
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $schema);
    }

    /**
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(self::RESOURCE_PATH, []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->collections[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): Collection
    {
        if (!isset($this->collections[$offset])) {
            $this->collections[$offset] = new Collection($offset, $this->apiCall);
        }

        return $this->collections[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->collections[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->collections[$offset]);
    }
}
