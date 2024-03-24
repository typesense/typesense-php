<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Synonym as SynonymObject;

class Synonym extends Request
{
    /**
     * Create or update a synonym.
     *
     * @param array{
     *     synonyms: array<int, string>,
     *     root?: string,
     *     locale?: string,
     *     symbols_to_index?: array<int, string>,
     * } $payload
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html#create-or-update-a-synonym
     */
    public function upsert(string $collection, string $id, array $payload): SynonymObject
    {
        $path = sprintf('/collections/%s/synonyms/%s', $collection, $id);

        $data = $this->send('PUT', $path, $payload);

        return SynonymObject::from($data);
    }

    /**
     * Retrieve a single synonym.
     *
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html#retrieve-a-synonym
     */
    public function retrieve(string $collection, string $id): SynonymObject
    {
        $path = sprintf('/collections/%s/synonyms/%s', $collection, $id);

        $data = $this->send('GET', $path);

        return SynonymObject::from($data);
    }

    /**
     * List all synonyms associated with a given collection.
     *
     * @return array<int, SynonymObject>
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html#list-all-synonyms
     */
    public function list(string $collection): array
    {
        $path = sprintf('/collections/%s/synonyms', $collection);

        $data = $this->send('GET', $path);

        return array_map(
            fn (stdClass $datum) => SynonymObject::from($datum),
            $data->synonyms,
        );
    }

    /**
     * Delete a synonym associated with a collection.
     *
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html#delete-a-synonym
     */
    public function delete(string $collection, string $id): bool
    {
        $path = sprintf('/collections/%s/synonyms/%s', $collection, $id);

        $data = $this->send('DELETE', $path);

        return $data->id === $id;
    }
}
