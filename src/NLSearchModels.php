<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class NLSearchModels
 *
 * @package \Typesense
 */
class NLSearchModels implements \ArrayAccess
{
    public const RESOURCE_PATH = '/nl_search_models';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $nlSearchModels = [];

    /**
     * NLSearchModels constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        if (isset($this->{$id})) {
            return $this->{$id};
        }
        if (!isset($this->nlSearchModels[$id])) {
            $this->nlSearchModels[$id] = new NLSearchModel($id, $this->apiCall);
        }

        return $this->nlSearchModels[$id];
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(array $params): array
    {
        return $this->apiCall->post(static::RESOURCE_PATH, $params);
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
        return isset($this->nlSearchModels[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): NLSearchModel
    {
        if (!isset($this->nlSearchModels[$offset])) {
            $this->nlSearchModels[$offset] = new NLSearchModel($offset, $this->apiCall);
        }

        return $this->nlSearchModels[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->nlSearchModels[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->nlSearchModels[$offset]);
    }
} 