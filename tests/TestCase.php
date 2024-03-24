<?php

declare(strict_types=1);

namespace Typesense\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Typesense\Typesense;

use function Pest\Faker\fake;

abstract class TestCase extends BaseTestCase
{
    public Typesense $typesense;

    public string $collectionName = 'testing';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->typesense = new Typesense([
            'url' => 'http://localhost:8108',
            'apiKey' => 'testing',
        ]);

        $this->createTestingCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->typesense->collection->drop($this->collectionName);

        parent::tearDown();
    }

    /**
     * Create testing collection.
     */
    protected function createTestingCollection(): void
    {
        $this->typesense->collection->create([
            'name' => $this->collectionName,
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                ],
            ],
        ]);
    }

    /**
     * Generate random slug.
     */
    public function slug(): string
    {
        return fake()->unique()->slug(variableNbWords: false);
    }
}
