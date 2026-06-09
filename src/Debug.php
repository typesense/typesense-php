<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Debug
 *
 * @package \Typesense
 * @date    10/12/20
 */
class Debug
{
    public const RESOURCE_PATH = '/debug';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Alias constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Retrieve server version and state information.
     *
     * @example
     * $client->debug->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#debug
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(Debug::RESOURCE_PATH, []);
    }
}
