<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

class Collection extends TypesenseObject
{
    /**
     * @var non-negative-int
     */
    public int $created_at;

    public string $default_sorting_field;

    public bool $enable_nested_fields;

    /**
     * @var array<int, CollectionField>
     */
    public array $fields;

    public string $name;

    /**
     * @var non-negative-int
     */
    public int $num_documents;

    /**
     * @var array<int, string>
     */
    public array $symbols_to_index;

    /**
     * @var array<int, string>
     */
    public array $token_separators;

    /**
     * {@inheritdoc}
     */
    public static function from(stdClass $data): static
    {
        $data->fields = array_map(
            fn (stdClass $data) => CollectionField::from($data),
            $data->fields,
        );

        return parent::from($data);
    }
}
