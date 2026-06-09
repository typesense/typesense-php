<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Metrics
 *
 * @package \Typesense
 * @date    10/12/20
 */
class Metrics
{
    public const RESOURCE_PATH = '/metrics.json';

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
     * Get current RAM, CPU, Disk & Network usage metrics.
     *
     * @example
     * $client->metrics->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(Metrics::RESOURCE_PATH, []);
    }
}
