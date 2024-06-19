<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Typesense\Client;

abstract class TestCase extends BaseTestCase
{
    private ?Client $typesenseClient = null;

    protected function setUp(): void
    {
        $this->setUpTypesenseClient();
    }

    protected function tearDown(): void
    {
        $this->tearDownTypesense();
    }

    protected function client(): Client
    {
        return $this->typesenseClient;
    }

    protected function getSchema(string $name): array
    {
        $path = __DIR__ . "/data/{$name}.schema.json";

        return $this->loadFromDataDir($path);
    }

    protected function getData(string $name): array
    {
        $path = __DIR__ . "/data/{$name}.data.json";

        return $this->loadFromDataDir($path);
    }

    private function loadFromDataDir(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        return json_decode(
            file_get_contents($path),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    private function setUpTypesenseClient(): void
    {
        $this->typesenseClient = new Client([
            'api_key' => $_ENV['TYPESENSE_API_KEY'],
            'nodes' => [
                [
                    'host' => $_ENV['TYPESENSE_NODE_HOST'],
                    'port' => $_ENV['TYPESENSE_NODE_PORT'],
                    'protocol' => $_ENV['TYPESENSE_NODE_PROTOCOL']
                ],
            ]
        ]);
    }

    protected function setUpCollection(string $schema): void
    {
        $schema = $this->getSchema($schema);
        $this->typesenseClient->collections->create($schema);
    }

    protected function setUpDocuments(string $schema): void
    {
        $documents = $this->getData($schema);
        $this->typesenseClient->collections[$schema]->documents->import($documents);
    }

    private function tearDownTypesense(): void
    {
        $collections = $this->typesenseClient->collections->retrieve();
        foreach ($collections as $collection) {
            $this->typesenseClient->collections[$collection['name']]->delete();
        }

        $aliases = $this->typesenseClient->aliases->retrieve();
        foreach ($aliases['aliases'] as $alias) {
            $this->typesenseClient->aliases[$alias['name']]->delete();
        }
    }
}
