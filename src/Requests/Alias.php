<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Alias as AliasObject;

class Alias extends Request
{
    /**
     * @param array{
     *     collection_name: string,
     * } $payload
     *
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/collection-alias.html#create-or-update-an-alias
     */
    public function upsert(string $name, array $payload): AliasObject
    {
        $path = sprintf('/aliases/%s', $name);

        $data = $this->send('PUT', $path, $payload);

        return AliasObject::from($data);
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/collection-alias.html#retrieve-an-alias
     */
    public function retrieve(string $name): AliasObject
    {
        $path = sprintf('/aliases/%s', $name);

        $data = $this->send('GET', $path);

        return AliasObject::from($data);
    }

    /**
     * @return array<int, AliasObject>
     *
     * @see https://typesense.org/docs/latest/api/collection-alias.html#list-all-aliases
     */
    public function list(): array
    {
        $data = $this->send('GET', '/aliases');

        return array_map(
            fn (stdClass $datum) => AliasObject::from($datum),
            $data->aliases,
        );
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/collection-alias.html#delete-an-alias
     */
    public function delete(string $name): bool
    {
        $path = sprintf('/aliases/%s', $name);

        $data = $this->send('DELETE', $path);

        return $data->name === $name;
    }
}
