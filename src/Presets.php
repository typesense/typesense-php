<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Document
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Presets
{
    /**
     * @var ApiCall
     */
    private $apiCall;

    public const PRESETS_PATH = '/presets';

    public const MULTI_SEARCH_PATH = '/multi_search';

    /**
     * Document constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
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
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function get()
    {
        return $this->apiCall->get(static::PRESETS_PATH, []);
    }

    /**
     * @param array $options
     *
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function put(array $options = [])
    {
        $presetName = $options['preset_name'];
        $presetsData = $options['preset_data'];

        return $this->apiCall->put($this->endpointPath($presetName), $presetsData);
    }

    /**
     * @param $presetName
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function delete($presetName)
    {
        return $this->apiCall->delete($this->endpointPath($presetName));
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
            $presetsName
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
}
