<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
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
            self::RESOURCE_PATH,
            $overrideId
        );
    }

    /**
     * @param string $documentId
     * @param array $config
     *
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function upsert(string $documentId, array $config): array
    {
        return $this->apiCall->put($this->endPointPath($documentId), $config);
    }

    /**
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($documentId): bool
    {
        return isset($this->overrides[$documentId]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($documentId)
    {
        if (!isset($this->overrides[$documentId])) {
            $this->overrides[$documentId] = new Override($this->collectionName, $documentId, $this->apiCall);
        }

        return $this->overrides[$documentId];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($documentId, $value): void
    {
        $this->overrides[$documentId] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($documentId): void
    {
        unset($this->overrides[$documentId]);
    }
}
