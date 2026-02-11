<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class SynonymSetItems
 *
 * @package \Typesense
 */
class SynonymSetItems implements \ArrayAccess
{
    /**
     * @var string
     */
    private string $synonymSetName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $items = [];

    /**
     * SynonymSetItems constructor.
     *
     * @param string $synonymSetName
     * @param ApiCall $apiCall
     */
    public function __construct(string $synonymSetName, ApiCall $apiCall)
    {
        $this->synonymSetName = $synonymSetName;
        $this->apiCall        = $apiCall;
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf(
            '%s/%s/items',
            SynonymSets::RESOURCE_PATH,
            encodeURIComponent($this->synonymSetName)
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
    public function offsetGet($itemId): SynonymSetItem
    {
        if (!isset($this->items[$itemId])) {
            $this->items[$itemId] = new SynonymSetItem($this->synonymSetName, $itemId, $this->apiCall);
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
