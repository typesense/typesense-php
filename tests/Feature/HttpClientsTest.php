<?php

namespace Feature;

use Tests\TestCase;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Typesense\Exceptions\ConfigError;
use Symfony\Component\HttpClient\Psr18Client;
use Typesense\Client;
use \stdClass;

class HttpClientsTest extends TestCase
{
    private array $baseConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseConfig = [
            'api_key' => $_ENV['TYPESENSE_API_KEY'],
            'nodes' => [[
                'host' => $_ENV['TYPESENSE_NODE_HOST'],
                'port' => $_ENV['TYPESENSE_NODE_PORT'],
                'protocol' => $_ENV['TYPESENSE_NODE_PROTOCOL']
            ]]
        ];
    }

    public function testWorksWithDefaultClient(): void
    {
        $client = new Client($this->baseConfig);
        $response = $client->health->retrieve();
        $this->assertIsBool($response['ok']);
    }

    public function testWorksWithPsr18Client(): void
    {
        $httpClient = new Psr18Client();
        $wrappedClient = new HttpMethodsClient(
            $httpClient,
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory()
        );

        $config = array_merge($this->baseConfig, ['client' => $wrappedClient]);
        $client = new Client($config);
        $response = $client->health->retrieve();
        $this->assertIsBool($response['ok']);
    }

    public function testWorksWithHttpMethodsClient(): void
    {
        $httpClient = new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory()
        );

        $config = array_merge($this->baseConfig, ['client' => $httpClient]);

        $client = new Client($config);
        $response = $client->health->retrieve();
        $this->assertIsBool($response['ok']);
    }

    public function testWorksWithLegacyPsr18Client(): void
    {
        $httpClient = $this->createMock(\Psr\Http\Client\ClientInterface::class);
        $config = array_merge($this->baseConfig, ['client' => $httpClient]);
        $client = new Client($config);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testRejectsInvalidClient(): void
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage('Client must implement PSR-18 ClientInterface or Http\Client\HttpClient');

        $config = array_merge($this->baseConfig, ['client' => new stdClass()]);
        new Client($config);
    }
}
