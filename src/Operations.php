<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Operations
 *
 * @package \Typesense
 */
class Operations
{
    public const RESOURCE_PATH = '/operations';

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
     * @param string $operationName
     * @param array $queryParameters
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function perform(string $operationName, array $queryParameters = []): array
    {
        return $this->apiCall->post(
            sprintf('%s/%s', static::RESOURCE_PATH, $operationName),
            null,
            true,
            $queryParameters
        );
    }
}
