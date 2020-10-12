<?php

namespace Typesense;

use Typesense\Lib\Configuration;

/**
 * Class Keys
 *
 * @package \Typesense
 * @date 6/1/20
 * @author Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Keys implements \ArrayAccess
{

    public const RESOURCE_PATH = '/keys';

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
    private array $keys = [];

    /**
     * Keys constructor.
     *
     * @param  \Typesense\Lib\Configuration  $config
     * @param  \Typesense\ApiCall  $apiCall
     */
    public function __construct(
      Configuration $config, ApiCall $apiCall
    ) {
        $this->config  = $config;
        $this->apiCall = $apiCall;
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
     * @param  string  $searchKey
     * @param  array  $parameters
     *
     * @return string
     * @throws \JsonException
     */
    public function generateScopedSearchKey(
      string $searchKey, array $parameters
    ): string {
        $paramStr     = json_encode($parameters, JSON_THROW_ON_ERROR);
        $digest       = base64_encode(hash_hmac('sha256', utf8_encode($paramStr), utf8_encode($searchKey)));
        $keyPrefix    = substr($searchKey, 0, 4);
        $rawScopedKey = sprintf('%s%s%s', utf8_decode($digest), $keyPrefix, $paramStr);
        return base64_encode(utf8_encode($rawScopedKey));
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
     * @param  mixed  $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->keys[$offset]);
    }

    /**
     * @param  mixed  $offset
     *
     * @return \Typesense\Key
     */
    public function offsetGet($offset): Key
    {
        if (!isset($this->keys[$offset])) {
            $this->keys[$offset] = new Key($this->config, $this->apiCall, $offset);
        }

        return $this->keys[$offset];
    }

    /**
     * @param  mixed  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->keys[$offset] = $value;
    }

    /**
     * @param  mixed  $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->keys[$offset]);
    }

}