<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Aliases
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Aliases implements \ArrayAccess
{
    public const RESOURCE_PATH = '/aliases';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $typesenseAliases = [];

    /**
     * Aliases constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param string $aliasName
     *
     * @return string
     */
    public function endPointPath(string $aliasName): string
    {
        return sprintf('%s/%s', static::RESOURCE_PATH, encodeURIComponent($aliasName));
    }

    /**
     * @param string $name
     * @param array $mapping
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(string $name, array $mapping): array
    {
        return $this->apiCall->put($this->endPointPath($name), $mapping);
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
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }

        if (!isset($this->typesenseAliases[$name])) {
            $this->typesenseAliases[$name] = new Alias($name, $this->apiCall);
        }

        return $this->typesenseAliases[$name];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->typesenseAliases[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): Alias
    {
        if (!isset($this->typesenseAliases[$offset])) {
            $this->typesenseAliases[$offset] = new Alias($offset, $this->apiCall);
        }

        return $this->typesenseAliases[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->typesenseAliases[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->typesenseAliases[$offset]);
    }
}
