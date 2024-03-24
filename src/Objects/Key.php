<?php

declare(strict_types=1);

namespace Typesense\Objects;

/**
 * @phpstan-type KeyAction 'collections:create'|'collections:delete'|'collections:get'|'collections:list'|'collections:*'|'documents:search'|'documents:get'|'documents:create'|'documents:upsert'|'documents:update'|'documents:delete'|'documents:import'|'documents:export'|'documents:*'|'aliases:list'|'aliases:get'|'aliases:create'|'aliases:delete'|'aliases:*'|'synonyms:list'|'synonyms:get'|'synonyms:create'|'synonyms:delete'|'synonyms:*'|'overrides:list'|'overrides:get'|'overrides:create'|'overrides:delete'|'overrides:*'|'keys:list'|'keys:get'|'keys:create'|'keys:delete'|'keys:*'|'metrics.json:list'|'stats.json:list'|'debug:list'|'*'
 */
class Key extends TypesenseObject
{
    /**
     * @var non-empty-array<int, KeyAction>
     */
    public array $actions;

    /**
     * @var non-empty-array<int, string>
     */
    public array $collections;

    public string $description;

    public int $expires_at;

    public int $id;

    public ?string $value = null;

    public ?string $value_prefix = null;
}
