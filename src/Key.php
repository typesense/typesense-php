<?php

namespace Typesence;

use \Typesence\Lib\Configuration;

/**
 * Class Key
 *
 * @package \Typesence
 * @date 6/1/20
 * @author Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Key
{

    /**
     * @var \Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var \Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var string
     */
    private $keyId;

    /**
     * Key constructor.
     *
     * @param  \Typesence\Lib\Configuration  $config
     * @param  \Typesence\ApiCall  $apiCall
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
     * @throws \Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * @return array
     * @throws \Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }

}