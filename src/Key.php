<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

/**
 * Class Key
 *
 * @package \Typesense
 * @date 6/1/20
 * @author Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Key
{

    /**
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var \Typesense\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var string
     */
    private string $keyId;

    /**
     * Key constructor.
     *
     * @param  \Typesense\Lib\Configuration  $config
     * @param  \Typesense\ApiCall  $apiCall
     * @param  string  $keyId
     */
    public function __construct(
      Configuration $config, ApiCall $apiCall, string $keyId
    ) {
        $this->config  = $config;
        $this->apiCall = $apiCall;
        $this->keyId   = $keyId;
    }

    /**
     * @return string
     */
    private function endpointPath(): string
    {
        return sprintf('%s/%s', Keys::RESOURCE_PATH, $this->keyId);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }

}