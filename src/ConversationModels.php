<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class ConversationModels
 *
 * @package \Typesense
 */
class ConversationModels implements \ArrayAccess
{
    public const RESOURCE_PATH = '/conversations/models';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $models = [];

    /**
     * ConversationModels constructor.
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
        if (!isset($this->models[$id])) {
            $this->models[$id] = new ConversationModel($id, $this->apiCall);
        }

        return $this->models[$id];
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
        return isset($this->models[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ConversationModel
    {
        if (!isset($this->models[$offset])) {
            $this->models[$offset] = new ConversationModel($offset, $this->apiCall);
        }

        return $this->models[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->models[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->models[$offset]);
    }
}
