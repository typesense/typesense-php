<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Overrides
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Overrides implements \ArrayAccess
{

    public const RESOURCE_PATH = 'overrides';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var array
     */
    private array $overrides = [];

    /**
     * Overrides constructor.
     *
     * @param string $collectionName
     * @param ApiCall $apiCall
     */
    public function __construct(string $collectionName, ApiCall $apiCall)
    {
        $this->collectionName = $collectionName;
        $this->apiCall        = $apiCall;
    }

    /**
     * @param string $overrideId
     *
     * @return string
     */
    public function endPointPath(string $overrideId = ''): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            Collections::RESOURCE_PATH,
            $this->collectionName,
            static::RESOURCE_PATH,
            $overrideId
        );
    }

    /**
     * @param string $overrideId
     * @param array $config
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function upsert(string $overrideId, array $config): array
    {
        return $this->apiCall->put($this->endPointPath($overrideId), $config);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($overrideId): bool
    {
        return isset($this->overrides[$overrideId]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($overrideId)
    {
        if (!isset($this->overrides[$overrideId])) {
            $this->overrides[$overrideId] = new Override($this->collectionName, $overrideId, $this->apiCall);
        }

        return $this->overrides[$overrideId];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($overrideId, $value): void
    {
        $this->overrides[$overrideId] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($overrideId): void
    {
        unset($this->overrides[$overrideId]);
    }
}
