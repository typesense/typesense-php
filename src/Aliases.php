<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
use Typesense\Exceptions\TypesenseClientError;
use Typesense\Lib\Configuration;

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
    private array $aliases = [];

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
        return sprintf('%s/%s', self::RESOURCE_PATH, $aliasName);
    }

    /**
     * @param string $name
     * @param array $mapping
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function upsert(string $name, array $mapping): array
    {
        return $this->apiCall->put($this->endPointPath($name), $mapping);
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
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }

        if (!isset($this->aliases[$name])) {
            $this->aliases[$name] = new Alias($name, $this->apiCall);
        }

        return $this->aliases[$name];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->aliases[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if (!isset($this->aliases[$offset])) {
            $this->aliases[$offset] = new Alias($offset, $this->apiCall);
        }

        return $this->aliases[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->aliases[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->aliases[$offset]);
    }
}
