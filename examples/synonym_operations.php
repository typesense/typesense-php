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
                        'facet' => true
                    ],
                    [
                        'name' => 'publication_year',
                        'type' => 'int32',
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
    echo "--------Upsert Synonym-------\n";
    print_r(
        $client->collections['books']->synonyms->upsert(
            'synonym-set-1',
            [
                'synonyms' => ['Hunger', 'Katniss'],
            ]
        )
    );
    echo "--------Upsert Synonym-------\n";
    echo "\n";
    echo "--------Get All Synonyms-------\n";
    print_r($client->collections['books']->synonyms->retrieve());
    echo "--------Get All Synonyms-------\n";
    echo "\n";
    echo "--------Get Single Synonym-------\n";
    print_r(
        $client->collections['books']->synonyms['synonym-set-1']->retrieve()
    );
    echo "--------Get Single Synonym-------\n";
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
                'title' => 'The Hunger Games',
                'image_url' => 'https://images.gr-assets.com/books/1447303603m/2767052.jpg',
                'ratings_count' => 4780653,
            ]
        )
    );
    echo "--------Create Document-------\n";
    echo "\n";
    echo "--------Search Document, using a synonym-------\n";
    print_r(
        $client->collections['books']->documents->search(
            [
                'q' => 'Katniss',
                'query_by' => 'title'
            ]
        )
    );
    echo "--------Search Document, using a synonym-------\n";
    echo "\n";
    echo "--------Upsert 1-way synonym-------\n";
    print_r(
        $client->collections['books']->synonyms->upsert(
            'synonym-set-1',
            [
                'root' => 'Katniss',
                'synonyms' => ['Hunger', 'Peeta'],
            ]
        )
    );
    echo "--------Upsert 1-way synonym-------\n";
    echo "\n";
    echo "--------Search Document, using a synonym-------\n";
    // Won't return any results
    print_r(
        $client->collections['books']->documents->search(
            [
                'q' => 'Peeta',
                'query_by' => 'title'
            ]
        )
    );
    echo "--------Search Document, using a synonym-------\n";
    echo "\n";
    echo "--------Delete Synonym-------\n";
    print_r(
        $client->collections['books']->getSynonyms()['synonym-set-1']->delete()
    );
    echo "--------Delete Synonym-------\n";
    echo "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
