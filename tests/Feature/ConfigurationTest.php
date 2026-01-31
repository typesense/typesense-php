<?php

namespace Feature;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Typesense\Exceptions\ConfigError;
use Typesense\Lib\Configuration;

class ConfigurationTest extends TestCase
{
    private array $baseConfig;

    protected function setUp(): void
    {
        $this->baseConfig = [
            'api_key' => 'test_api_key',
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
        ];
    }

    public function testConfigurationWithDefaultLogger(): void
    {
        $config = new Configuration($this->baseConfig);

        $logger = $config->getLogger();

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testConfigurationWithCustomLogger(): void
    {
        // Create a custom logger
        $customLogger = new Logger('custom-test-logger');
        $customLogger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

        // Add custom logger to config
        $configWithCustomLogger = array_merge($this->baseConfig, [
            'logger' => $customLogger,
        ]);

        $config = new Configuration($configWithCustomLogger);

        $logger = $config->getLogger();

        // Assert that the logger is the same instance we passed
        $this->assertSame($customLogger, $logger);
        $this->assertEquals('custom-test-logger', $logger->getName());
    }

    public function testConfigurationWithCustomLogLevel(): void
    {
        // Add custom log level to config
        $configWithLogLevel = array_merge($this->baseConfig, [
            'log_level' => Logger::DEBUG,
        ]);

        $config = new Configuration($configWithLogLevel);

        $logger = $config->getLogger();
        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testConfigurationWithCustomLoggerThrowsExceptionWhenLogLevelIsAlsoProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Setting log_level is not allowed when a custom logger is provided.');

        $customLogger = new Logger('custom-logger-with-level');
        $customLogger->pushHandler(new StreamHandler('php://stdout', Logger::ERROR));

        $configWithBoth = array_merge($this->baseConfig, [
            'logger' => $customLogger,
            'log_level' => Logger::DEBUG,
        ]);

        new Configuration($configWithBoth);
    }

    public function testConfigurationWithInvalidLoggerThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Logger must implement Psr\Log\LoggerInterface');

        // Try to pass a non-logger object (should throw exception)
        $configWithInvalidLogger = array_merge($this->baseConfig, [
            'logger' => 'not-a-logger-instance',
        ]);

        new Configuration($configWithInvalidLogger);
    }

    public function testConfigurationThrowsErrorWhenNodesAreMissing(): void
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage('`nodes` is not defined.');

        new Configuration([
            'api_key' => 'test_api_key',
        ]);
    }

    public function testConfigurationThrowsErrorWhenApiKeyIsMissing(): void
    {
        $this->expectException(ConfigError::class);
        $this->expectExceptionMessage('`api_key` is not defined.');

        new Configuration([
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
        ]);
    }

    public function testConfigurationWithAllOptions(): void
    {
        $customLogger = new Logger('full-config-logger');
        $customLogger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

        $fullConfig = [
            'api_key' => 'test_api_key',
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                    'path' => '/api',
                ],
            ],
            'nearest_node' => [
                'host' => 'nearest.example.com',
                'port' => '443',
                'protocol' => 'https',
            ],
            'logger' => $customLogger,
            'num_retries' => 5,
            'retry_interval_seconds' => 2.0,
            'healthcheck_interval_seconds' => 30,
            'randomize_nodes' => false,
        ];

        $config = new Configuration($fullConfig);

        $this->assertSame($customLogger, $config->getLogger());
        $this->assertEquals(5, $config->getNumRetries());
        $this->assertEquals(2.0, $config->getRetryIntervalSeconds());
        $this->assertEquals(30, $config->getHealthCheckIntervalSeconds());
        $this->assertNotNull($config->getNearestNode());
    }
}

