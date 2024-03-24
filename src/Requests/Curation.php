<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Curation as CurationObject;

/**
 * @phpstan-import-type CurationRule from CurationObject
 * @phpstan-import-type CurationExclude from CurationObject
 * @phpstan-import-type CurationInclude from CurationObject
 */
class Curation extends Request
{
    /**
     * Create or update an override.
     *
     * @param array{
     *     rule: CurationRule,
     *     includes?: array<int, CurationInclude>,
     *     excludes?: array<int, CurationExclude>,
     *     filter_by?: string,
     *     sort_by?: string,
     *     replace_query?: string,
     *     remove_matched_tokens?: bool,
     *     filter_curated_hits?: bool,
     *     effective_from_ts?: int,
     *     effective_to_ts?: int,
     *     stop_processing?: bool,
     * } $payload
     *
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/curation.html#create-or-update-an-override
     */
    public function upsert(string $collection, string $id, array $payload): CurationObject
    {
        $path = sprintf('/collections/%s/overrides/%s', $collection, $id);

        $data = $this->send('PUT', $path, $payload);

        return CurationObject::from($data);
    }

    /**
     * Fetch an individual override associated with a collection.
     *
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/curation.html#retrieve-an-override
     */
    public function retrieve(string $collection, string $id): CurationObject
    {
        $path = sprintf('/collections/%s/overrides/%s', $collection, $id);

        $data = $this->send('GET', $path);

        return CurationObject::from($data);
    }

    /**
     * Listing all overrides associated with a given collection.
     *
     * @return array<int, CurationObject>
     *
     * @see https://typesense.org/docs/latest/api/curation.html#list-all-overrides
     */
    public function list(string $collection): array
    {
        $path = sprintf('/collections/%s/overrides', $collection);

        $data = $this->send('GET', $path);

        return array_map(
            fn (stdClass $datum) => CurationObject::from($datum),
            $data->overrides,
        );
    }

    /**
     * Deleting an override associated with a collection.
     *
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/curation.html#delete-an-override
     */
    public function delete(string $collection, string $id): bool
    {
        $path = sprintf('/collections/%s/overrides/%s', $collection, $id);

        $data = $this->send('DELETE', $path);

        return $data->id === $id;
    }
}
