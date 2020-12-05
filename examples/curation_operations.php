<?php

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
            'connection_timeout_seconds' => 2,
            'client' => new HttplugClient(),
        ]
    );
    echo '<pre>';
    try {
        print_r($client->collections['books']->delete());
    } catch (Exception $e) {
        // Don't error out if the collection was not found
    }
    echo "--------Create Collection-------\n";
    print_r(
        $client->collections->create(
            [
                'name' => 'books',
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'authors',
                        'type' => 'string[]',
                    ],
                    [
                        'name' => 'authors_facet',
                        'type' => 'string[]',
                        'facet' => true,
                    ],
                    [
                        'name' => 'publication_year',
                        'type' => 'int32',
                    ],
                    [
                        'name' => 'publication_year_facet',
                        'type' => 'string',
                        'facet' => true,
                    ],
                    [
                        'name' => 'ratings_count',
                        'type' => 'int32',
                    ],
                    [
                        'name' => 'average_rating',
                        'type' => 'float',
                    ],
                    [
                        'name' => 'image_url',
                        'type' => 'string',
                    ],
                ],
                'default_sorting_field' => 'ratings_count',
            ]
        )
    );
    echo "--------Create Collection-------\n";
    echo "\n";
    echo "--------Create or Update Override-------\n";
    print_r(
        $client->collections['books']->overrides->upsert(
            'hermione-exact',
            [
                'rule' => [
                    'query' => 'hermione',
                    'match' => 'exact',
                ],
                'includes' => [
                    [
                        'id' => '1',
                        'position' => 1,
                    ],
                ],
            ]
        )
    );
    echo "--------Create or Update Override-------\n";
    echo "\n";
    echo "--------Get All Overrides-------\n";
    print_r($client->collections['books']->overrides->retrieve());
    echo "--------Get All Overrides-------\n";
    echo "\n";
    echo "--------Get Single Override-------\n";
    print_r(
        $client->collections['books']->overrides['hermione-exact']->retrieve()
    );
    echo "--------Get Single Override-------\n";
    echo "\n";
    echo "--------Create Document-------\n";
    print_r(
        $client->collections['books']->documents->create(
            [
                'id' => '1',
                'original_publication_year' => 2008,
                'authors' => [
                    'Suzanne Collins',
                ],
                'average_rating' => 4.34,
                'publication_year' => 2008,
                'publication_year_facet' => '2008',
                'authors_facet' => [
                    'Suzanne Collins',
                ],
                'title' => 'The Hunger Games',
                'image_url' => 'https://images.gr-assets.com/books/1447303603m/2767052.jpg',
                'ratings_count' => 4780653,
            ]
        )
    );
    echo "--------Create Document-------\n";
    echo "\n";
    echo "--------Search Document-------\n";
    print_r(
        $client->collections['books']->documents->search(
            [
                'q' => 'hermione',
                'query_by' => 'title',
                'sort_by' => 'ratings_count:desc',
            ]
        )
    );
    echo "--------Search Document-------\n";
    echo "\n";
    echo "--------Delete Override-------\n";
    print_r(
        $client->collections['books']->getOverrides()['hermione-exact']->delete()
    );
    echo "--------Delete Override-------\n";
    echo "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
