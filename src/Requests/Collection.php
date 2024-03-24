<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceAlreadyExistsException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Objects\Collection as CollectionObject;
use Typesense\Objects\CollectionDroppedField;
use Typesense\Objects\CollectionField;

/**
 * @phpstan-import-type TypesenseCollectionFieldLocale from CollectionField
 * @phpstan-import-type TypesenseCollectionFieldType from CollectionField
 *
 * @phpstan-type CollectionFieldCreation array{
 *     name: string,
 *     type: TypesenseCollectionFieldType,
 *     optional?: bool,
 *     facet?: bool,
 *     index?: bool,
 *     infix?: bool,
 *     sort?: bool,
 *     locale?: TypesenseCollectionFieldLocale,
 * }
 */
class Collection extends Request
{
    /**
     * @param array{
     *     name: string,
     *     fields: non-empty-array<int, CollectionFieldCreation>,
     *     enable_nested_fields?: bool,
     *     token_separators?: array<int, string>,
     *     symbols_to_index?: array<int, string>,
     *     default_sorting_field?: string,
     * } $payload
     *
     * @throws InvalidPayloadException
     * @throws ResourceAlreadyExistsException
     *
     * @see https://typesense.org/docs/latest/api/collections.html#create-a-collection
     */
    public function create(array $payload): CollectionObject
    {
        $data = $this->send('POST', '/collections', $payload);

        return CollectionObject::from($data);
    }

    /**
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/collections.html#cloning-a-collection-schema
     */
    public function clone(string $source, string $name): CollectionObject
    {
        $query = http_build_query([
            'src_name' => $source,
        ]);

        $path = sprintf('/collections?%s', $query);

        $payload = [
            'name' => $name,
        ];

        $data = $this->send('POST', $path, $payload);

        return CollectionObject::from($data);
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/collections.html#retrieve-a-collection
     */
    public function retrieve(string $name): CollectionObject
    {
        $path = sprintf('/collections/%s', $name);

        $data = $this->send('GET', $path);

        return CollectionObject::from($data);
    }

    /**
     * @return array<int, CollectionObject>
     *
     * @see https://typesense.org/docs/latest/api/collections.html#list-all-collections
     */
    public function list(): array
    {
        $data = $this->send('GET', '/collections', expectArray: true);

        return array_map(
            fn (stdClass $datum) => CollectionObject::from($datum),
            $data,
        );
    }

    /**
     * @throws ResourceNotFoundException
     *
     * @see https://typesense.org/docs/latest/api/collections.html#drop-a-collection
     */
    public function drop(string $name): CollectionObject
    {
        $path = sprintf('/collections/%s', $name);

        $data = $this->send('DELETE', $path);

        return CollectionObject::from($data);
    }

    /**
     * @param  array<int, CollectionFieldCreation|array{name: string, drop: true}>  $fields
     * @return array<int, CollectionField|CollectionDroppedField>
     *
     * @throws ResourceNotFoundException
     * @throws InvalidPayloadException
     *
     * @see https://typesense.org/docs/latest/api/collections.html#update-or-alter-a-collection
     */
    public function update(string $name, array $fields): array
    {
        $path = sprintf('/collections/%s', $name);

        $data = $this->send('PATCH', $path, ['fields' => $fields]);

        return array_map(function (stdClass $field) {
            if (isset($field->drop)) {
                return CollectionDroppedField::from($field);
            }

            return CollectionField::from($field);
        }, $data->fields);
    }
}
