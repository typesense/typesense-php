<?php

include '../vendor/autoload.php';

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
            'connection_timeout_seconds' => 2,
        ]
    );
    echo '<pre>';
    try {
        print_r($client->collections['users']->delete());
    } catch (Exception $e) {
        // Don't error out if the collection was not found
    }
    echo "--------Create Collection-------\n";
    print_r(
        $client->collections->create(
            [
                'name' => 'users',
                'fields' => [
                    [
                        'name' => 'company_id',
                        'type' => 'int32',
                        'facet' => false
                    ],
                    [
                        'name' => 'user_name',
                        'type' => 'string',
                        'facet' => false
                    ],
                    [
                        'name' => 'login_count',
                        'type' => 'int32',
                        'facet' => false
                    ],
                    [
                        'name' => 'country',
                        'type' => 'string',
                        'facet' => true
                    ]
                ],
                'default_sorting_field' => 'company_id'
            ]
        )
    );
    echo "--------Create Collection-------\n";
    echo "\n";
    echo "--------Create Documents-------\n";
    print_r(
        $client->collections['users']->documents->createMany([
            [
                'company_id' => 124,
                'user_name' => 'Hilary Bradford',
                'login_count' => 10,
                'country' => 'USA'
            ],
            [
                'company_id' => 124,
                'user_name' => 'Nile Carty',
                'login_count' => 100,
                'country' => 'USA'
            ],
            [
                'company_id' => 126,
                'user_name' => 'Tahlia Maxwell',
                'login_count' => 1,
                'country' => 'France'
            ],
            [
                'company_id' => 126,
                'user_name' => 'Karl Roy',
                'login_count' => 2,
                'country' => 'Germany'
            ]
        ])
    );
    echo "--------Create Documents-------\n";
    echo "\n";
    echo "--------Create a search only API key-------\n";
    $searchOnlyApiKeyResponse = $client->keys->create([
        'description' => 'Search-only key.',
        'actions' => ['documents:search'],
        'collections' => ['*']
    ]);
    print_r($searchOnlyApiKeyResponse);
    echo "--------Create a search only API key-------\n";
    echo "\n";
    echo "--------Get All Keys-------\n";
    print_r($client->keys->retrieve());
    echo "--------Get All Keys-------\n";
    echo "\n";
    echo "--------Get Single Key-------\n";
    print_r(
        $client->keys[$searchOnlyApiKeyResponse['id']]->retrieve()
    );
    echo "--------Get Single Key-------\n";
    echo "\n";
    echo "--------Generate Scoped API Key-------\n";
    $scopedAPIKey = $client->keys->generateScopedSearchKey($searchOnlyApiKeyResponse['value'], ['filter_by' => 'company_id:124']);
    print_r($scopedAPIKey);
    echo "\n";
    echo "--------Generate Scoped API Key-------\n";
    echo "\n";
    echo "--------Search Documents with scoped Key-------\n";
    $scopedClient = new Client(
        [
            'api_key' => $scopedAPIKey,
            'nodes' => [
                [
                    'host' => 'localhost',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]
    );

    print_r(
        $scopedClient->collections['users']->documents->search(
            [
                'q' => 'Hilary',
                'query_by' => 'user_name'
            ]
        )
    );
    echo "--------Search Documents with scoped Key-------\n";
    echo "\n";
    echo "--------Search for document outside of scope for scoped Key-------\n";
    print_r(
        $scopedClient->collections['users']->documents->search(
            [
                'q' => 'Maxwell',
                'query_by' => 'user_name'
            ]
        )
    );
    echo "--------Search for document outside of scope for scoped Key-------\n";
    echo "\n";
    echo "--------Delete Key-------\n";
    print_r(
        $client->keys[$searchOnlyApiKeyResponse['id']]->delete()
    );
    echo "--------Delete Key-------\n";
    echo "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
