<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Key as KeyObject;

/**
 * @phpstan-import-type KeyAction from KeyObject
 */
class Key extends Request
{
    /**
     * @param array{
     *     actions: array<int, KeyAction>,
     *     collections: array<int, string>,
     *     description: string,
     *     value?: string,
     *     expires_at?: int,
     * } $payload
     *
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#create-an-api-key
     */
    public function create(array $payload): KeyObject
    {
        $data = $this->send('POST', '/keys', $payload);

        return KeyObject::from($data);
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#retrieve-an-api-key
     */
    public function retrieve(int $id): KeyObject
    {
        $path = sprintf('/keys/%d', $id);

        $data = $this->send('GET', $path);

        return KeyObject::from($data);
    }

    /**
     * @return array<int, KeyObject>
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#list-all-keys
     */
    public function list(): array
    {
        $data = $this->send('GET', '/keys');

        return array_map(
            fn (stdClass $datum) => KeyObject::from($datum),
            $data->keys,
        );
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html#delete-api-key
     */
    public function delete(int $id): bool
    {
        $path = sprintf('/keys/%d', $id);

        $data = $this->send('DELETE', $path);

        return $data->id === $id;
    }
}
