<?php

namespace Typesense;

use GuzzleHttp\Exception\GuzzleException;
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
     * @return array
     * @throws TypesenseClientError|GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(Metrics::RESOURCE_PATH, []);
    }
}
