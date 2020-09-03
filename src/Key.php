<?php


namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Key
 *
 * @package Devloops\Typesence
 * @date 6/1/20
 * @author Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Key
{

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $congif;

    /**
     * @var \Devloops\Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var string
     */
    private $keyId;

    /**
     * Key constructor.
     *
     * @param  \Devloops\Typesence\Lib\Configuration  $congif
     * @param  \Devloops\Typesence\ApiCall  $apiCall
     * @param  string  $keyId
     */
    public function __construct(
      Configuration $congif, ApiCall $apiCall, string $keyId
    ) {
        $this->congif  = $congif;
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
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }

}