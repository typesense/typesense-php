<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class MultiSearch
 *
 * @package \Typesense
 */
class MultiSearch
{
    public const RESOURCE_PATH = '/multi_search';

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
     * @param string $searches
     * @param array $queryParameters
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function perform(array $searches, array $queryParameters = []): array
    {
        return $this->apiCall->post(
            sprintf('%s', static::RESOURCE_PATH),
            $searches,
            true,
            $queryParameters
        );
    }
}
