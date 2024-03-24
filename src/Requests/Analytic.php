<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceAlreadyExistsException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Analytic as AnalyticObject;

/**
 * @phpstan-import-type RulePayload from AnalyticObject
 */
class Analytic extends Request
{
    /**
     * Create a collection for aggregation.
     *
     * @throws ResourceAlreadyExistsException
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html#create-a-collection-for-aggregation
     */
    public function setup(string $name): bool
    {
        $data = $this->send('POST', '/collections', [
            'name' => $name,
            'fields' => [
                ['name' => 'q', 'type' => 'string'],
                ['name' => 'count', 'type' => 'int32'],
            ],
        ]);

        return $data->name === $name;
    }

    /**
     * @param array{
     *     name: string,
     *     type: 'popular_queries',
     *     params: RulePayload,
     * } $payload
     *
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html#create-an-analytics-rule
     */
    public function create(array $payload): AnalyticObject
    {
        $data = $this->send('POST', '/analytics/rules', $payload);

        return AnalyticObject::from($data);
    }

    /**
     * @return array<int, AnalyticObject>
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html#list-all-rules
     */
    public function list(): array
    {
        $data = $this->send('GET', '/analytics/rules');

        return array_map(
            fn (stdClass $datum) => AnalyticObject::from($datum),
            $data->rules,
        );
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html#remove-a-rule
     */
    public function delete(string $name): bool
    {
        $path = sprintf('/analytics/rules/%s', $name);

        $data = $this->send('DELETE', $path);

        return $data->name === $name;
    }
}
