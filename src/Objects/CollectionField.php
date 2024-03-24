<?php

declare(strict_types=1);

namespace Typesense\Objects;

/**
 * @phpstan-type TypesenseCollectionFieldType 'string'|'string[]'|'int32'|'int32[]'|'int64'|'int64[]'|'float'|'float[]'|'bool'|'bool[]'|'geopoint'|'geopoint[]'|'object'|'object[]'|'string*'|'auto'
 * @phpstan-type TypesenseCollectionFieldLocale 'ja'|'zh'|'ko'|'th'|'el'|'ru'|'sr'|'uk'|'be'
 * @phpstan-type TypesenseCollectionField array{
 *     embed: mixed,
 *     facet: bool,
 *     index: bool,
 *     infix: bool,
 *     locale: TypesenseCollectionFieldLocale,
 *     name: string,
 *     nested: bool,
 *     nested_array: int,
 *     num_dim: int,
 *     optional: bool,
 *     reference: bool,
 *     sort: bool,
 *     type: TypesenseCollectionFieldType,
 *     vec_dist: TypesenseCollectionFieldType,
 * }
 */
class CollectionField extends TypesenseObject
{
    public mixed $embed;

    public bool $facet;

    public bool $index;

    public bool $infix;

    /**
     * @var TypesenseCollectionFieldLocale|''
     */
    public string $locale;

    public string $name;

    public bool $nested;

    public int $nested_array;

    public int $num_dim;

    public bool $optional;

    public string $reference;

    public bool $sort;

    /**
     * @var TypesenseCollectionFieldType
     */
    public string $type;

    public string $vec_dist;
}
