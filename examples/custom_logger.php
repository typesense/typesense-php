<?php

include '../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttplugClient;
use Typesense\Client;

/**
 * Example: Using a custom logger with Typesense
 *
 * This example demonstrates how to inject your own logger instance
 * instead of using the default Monolog logger.
 */

try {
    echo '<pre>';

    // Create your custom logger instance
    $customLogger = new Logger('my-custom-logger');
    $customLogger->pushHandler(new StreamHandler('/tmp/typesense-custom.log', Logger::DEBUG));

    // You can also use any other PSR-3 compatible logger
    // For example: Symfony's Logger, Laravel's Logger, etc.

    echo "--------Example 1: Client with Custom Logger-------\n";
    // Initialize Typesense client with custom logger
    $client = new Client(
        [
            'api_key' => 'xyz',
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
            'client' => new HttplugClient(),
            'logger' => $customLogger,  // Inject your custom logger here
        ]
    );

    // Use the client - all logs will now use your custom logger
    $health = $client->health->retrieve();
    print_r($health);
    echo "✓ Using custom logger - logs written to /tmp/typesense-custom.log\n";

    echo "\n--------Example 2: Client with Default Logger-------\n";
    // Example without custom logger (uses default):
    $clientWithDefaultLogger = new Client(
        [
            'api_key' => 'xyz',
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
            'client' => new HttplugClient(),
            'log_level' => Logger::INFO,  // You can customize the log level
        ]
    );

    $health2 = $clientWithDefaultLogger->health->retrieve();
    print_r($health2);
    echo "✓ Using default logger - logs written to stdout\n";

} catch (Exception $e) {
    echo $e->getMessage();
}

