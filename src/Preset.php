<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Preset
 *
 * @package \Typesense
 */
class Preset
{
    /**
     * @var string
     */
    private string $presetName;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Preset constructor.
     *
     * @param string  $presetName
     * @param ApiCall $apiCall
     */
    public function __construct(string $presetName, ApiCall $apiCall)
    {
        $this->presetName = $presetName;
        $this->apiCall = $apiCall;
    }

    /**
     * Retrieve the details of a preset, given its name.
     *
     * @example
     * $client->presets['listing_view']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * Permanently deletes a preset, given its name.
     *
     * @example
     * $client->presets['listing_view']->delete()
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

    /**
     * @return string
     */
    public function endPointPath(): string
    {
        return sprintf('%s/%s', Presets::PRESETS_PATH, $this->presetName);
    }
}
