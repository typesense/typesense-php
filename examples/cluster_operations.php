<?php

/** @noinspection ForgottenDebugOutputInspection */

include '../vendor/autoload.php';

use Symfony\Component\HttpClient\HttplugClient;
use Typesense\Client;

try {
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
        ]
    );
    echo '<pre>';

    print_r($client->operations->perform('snapshot', ['snapshot_path' => '/tmp/snapshot']));
} catch (Exception $e) {
    echo $e->getMessage();
}
