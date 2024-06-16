<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

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
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $keys = [];

    /**
     * Keys constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param array $schema
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(array $schema): array
    {
        return $this->apiCall->post(static::RESOURCE_PATH, $schema);
    }

    /**
     * @param string $searchKey
     * @param array $parameters
     *
     * @return string
     * @throws \JsonException
     */
    public function generateScopedSearchKey(
        string $searchKey,
        array $parameters
    ): string {
        $paramStr     = json_encode($parameters, JSON_THROW_ON_ERROR);
        $digest       = base64_encode(
            hash_hmac(
                'sha256',
                mb_convert_encoding($paramStr, 'UTF-8', 'ISO-8859-1'),
                mb_convert_encoding($searchKey, 'UTF-8', 'ISO-8859-1'),
                true
            )
        );
        $keyPrefix    = substr($searchKey, 0, 4);
        $rawScopedKey = sprintf(
            '%s%s%s',
            mb_convert_encoding($digest, 'ISO-8859-1', 'UTF-8'),
            $keyPrefix,
            $paramStr
        );
        return base64_encode(mb_convert_encoding($rawScopedKey, 'UTF-8', 'ISO-8859-1'));
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
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->keys[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return \Typesense\Key
     */
    public function offsetGet($offset): Key
    {
        if (!isset($this->keys[$offset])) {
            $this->keys[$offset] = new Key($offset, $this->apiCall);
        }

        return $this->keys[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->keys[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->keys[$offset]);
    }
}
