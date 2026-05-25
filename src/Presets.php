<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Presets
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Presets implements \ArrayAccess
{
    /**
     * @var ApiCall
     */
    private $apiCall;

    public const PRESETS_PATH = '/presets';

    public const MULTI_SEARCH_PATH = '/multi_search';

    /**
     * @var array
     */
    private array $typesensePresets = [];

    /**
     * Presets constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Run a multi-search request using a previously saved preset.
     *
     * @example
     * $client->presets->searchWithPreset('listing_view')
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @param $presetName
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function searchWithPreset($presetName)
    {
        return $this->apiCall->post($this->multiSearchEndpointPath(), [], true, ['preset' => $presetName]);
    }

    /**
     * Retrieve the details of all presets.
     *
     * @example
     * $client->presets->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function retrieve()
    {
        return $this->apiCall->get(static::PRESETS_PATH, []);
    }

    /**
     * Create or update an existing preset.
     *
     * @example
     * $client->presets->upsert('listing_view', ['value' => ['q' => '*']])
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @param string $presetName
     * @param array $presetsData
     *
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function upsert(string $presetName, array $presetsData)
    {
        return $this->apiCall->put($this->endpointPath($presetName), $presetsData);
    }

    /**
     * @param $presetsName
     * @return string
     */
    private function endpointPath($presetsName)
    {
        return sprintf(
            '%s/%s',
            static::PRESETS_PATH,
            encodeURIComponent($presetsName)
        );
    }

    /**
     * @param $presetsName
     * @return string
     */
    private function multiSearchEndpointPath()
    {
        return sprintf(
            '%s',
            static::MULTI_SEARCH_PATH
        );
    }

    /**
     * @param $presetName
     *
     * @return mixed
     */
    public function __get($presetName)
    {
        if (isset($this->{$presetName})) {
            return $this->{$presetName};
        }
        if (!isset($this->typesensePresets[$presetName])) {
            $this->typesensePresets[$presetName] = new Preset($presetName, $this->apiCall);
        }

        return $this->typesensePresets[$presetName];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->typesensePresets[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): Preset
    {
        if (!isset($this->typesensePresets[$offset])) {
            $this->typesensePresets[$offset] = new Preset($offset, $this->apiCall);
        }

        return $this->typesensePresets[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->typesensePresets[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->typesensePresets[$offset]);
    }
}
