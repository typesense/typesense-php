<?php


namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Aliases
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Aliases implements \ArrayAccess
{

    public const RESOURCE_PATH = '/aliases';

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var \Devloops\Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * Aliases constructor.
     *
     * @param   \Devloops\Typesence\Lib\Configuration  $config
     */
    public function __construct(Configuration $config)
    {
        $this->config  = $config;
        $this->apiCall = new ApiCall($this->config);
    }

    /**
     * @param   string  $aliasName
     *
     * @return string
     */
    public function endPointPath(string $aliasName): string
    {
        return sprintf('%s/%s', self::RESOURCE_PATH, $aliasName);
    }

    /**
     * @param   string  $name
     * @param   array   $mapping
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function upsert(string $name, array $mapping): array
    {
        return $this->apiCall->put($this->endPointPath($name), $mapping);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(
          $this->endPointPath(self::RESOURCE_PATH),
          []
        );
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
            $this->aliases[$name] = new Alias($this->config, $name);
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
            $this->aliases[$offset] = new Alias($this->config, $offset);
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