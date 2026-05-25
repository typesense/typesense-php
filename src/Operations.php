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
     * Perform a cluster operation: snapshot, vote, cache/clear, db/compact, or a custom path.
     *
     * @example
     * $client->operations->perform('snapshot', ['snapshot_path' => '/tmp/snap'])
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html
     *
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

    /**
     * Get the status of in-progress schema change operations.
     *
     * @example
     * $client->operations->getSchemaChangeStatus()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function getSchemaChangeStatus(): array
    {
        return $this->apiCall->get(sprintf('%s/%s', static::RESOURCE_PATH, 'schema_changes'), []);
    }
}
