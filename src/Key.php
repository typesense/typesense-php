<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
use Typesense\Exceptions\TypesenseClientError;

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
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var string
     */
    private string $keyId;

    /**
     * Key constructor.
     *
     * @param string $keyId
     * @param ApiCall $apiCall
     */
    public function __construct(string $keyId, ApiCall $apiCall)
    {
        $this->keyId   = $keyId;
        $this->apiCall = $apiCall;
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
     * @throws TypesenseClientError|GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }
}
