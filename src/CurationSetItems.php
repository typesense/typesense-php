<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class CurationSetItems
 *
 * @package \Typesense
 */
class CurationSetItems implements \ArrayAccess
{
    /**
     * @var string
     */
    private string $curationSetName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $items = [];

    /**
     * CurationSetItems constructor.
     *
     * @param string $curationSetName
     * @param ApiCall $apiCall
     */
    public function __construct(string $curationSetName, ApiCall $apiCall)
    {
        $this->curationSetName = $curationSetName;
        $this->apiCall         = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s/items',
            CurationSets::RESOURCE_PATH,
            encodeURIComponent($this->curationSetName)
        );
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
    public function offsetExists($itemId): bool
    {
        return isset($this->items[$itemId]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($itemId): CurationSetItem
    {
        if (!isset($this->items[$itemId])) {
            $this->items[$itemId] = new CurationSetItem($this->curationSetName, $itemId, $this->apiCall);
        }

        return $this->items[$itemId];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($itemId, $value): void
    {
        $this->items[$itemId] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($itemId): void
    {
        unset($this->items[$itemId]);
    }
}
