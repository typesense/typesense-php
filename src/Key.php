<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
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
        return sprintf('%s/%s', Keys::RESOURCE_PATH, encodeURIComponent($this->keyId));
    }

    /**
     * Retrieve (metadata about) a key. Only the key prefix is returned when you retrieve a key. Due to security reasons, only the create endpoint returns the full API key.
     *
     * @example
     * $client->keys[1]->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#retrieve-an-api-key
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * Delete an API key given its ID.
     *
     * @example
     * $client->keys[1]->delete()
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#delete-api-key
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endpointPath());
    }
}
