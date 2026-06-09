<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Health
 *
 * @package \Typesense
 * @date    10/12/20
 */
class Health
{
    public const RESOURCE_PATH = '/health';

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
     * Checks if Typesense server is ready to accept requests.
     *
     * @example
     * $client->health->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#health
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(Health::RESOURCE_PATH, []);
    }
}
