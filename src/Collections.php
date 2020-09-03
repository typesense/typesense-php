<?php


namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Collections
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collections implements \ArrayAccess
{

    public const RESOURCE_PATH = '/collections';

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $congif;

    /**
     * @var \Devloops\Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var array
     */
    private $collections = [];

    /**
     * Collections constructor.
     *
     * @param $congif
     */
    public function __construct(Configuration $congif)
    {
        $this->congif  = $congif;
        $this->apiCall = new ApiCall($congif);
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
            $this->collections[$collectionName] = new Collection($this->congif, $collectionName);
        }

        return $this->collections[$collectionName];
    }

    /**
     * @param  array  $schema
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $schema): array
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $schema);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
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
            $this->collections[$offset] = new Collection($this->congif, $offset);
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