<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

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
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var \Typesense\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $collections = [];

    /**
     * Collections constructor.
     *
     * @param $config
     */
    public function __construct(Configuration $config)
    {
        $this->config  = $config;
        $this->apiCall = new ApiCall($config);
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
            $this->collections[$collectionName] = new Collection($this->config, $collectionName);
        }

        return $this->collections[$collectionName];
    }

    /**
     * @param  array  $schema
     *
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $schema): array
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $schema);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
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
            $this->collections[$offset] = new Collection($this->config, $offset);
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