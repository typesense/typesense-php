<?php

/** @noinspection ForgottenDebugOutputInspection */

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
    echo "--------Retrieve Collection-------\n";
    print_r($client->collections['books']->retrieve());
    echo "--------Retrieve Collection-------\n";
    echo "\n";
    echo "--------Retrieve All Collections-------\n";
    print_r($client->collections->retrieve());
    echo "--------Retrieve All Collections-------\n";
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

    echo "--------Upsert Document-------\n";
    print_r(
        $client->collections['books']->documents->upsert(
            [
                'id' => '1',
                'original_publication_year' => 2008,
                'authors' => [
                    'Suzanne Collins',
                ],
                'average_rating' => 4.6,
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
    echo "--------Upsert Document-------\n";
    echo "\n";

    echo "--------Export Documents-------\n";
    $exportedDocStrs = $client->collections['books']->documents->export();
    print_r($exportedDocStrs);
    echo "--------Export Documents-------\n";
    echo "\n";
    echo "--------Update Single Document-------\n";
    print_r($client->collections['books']->documents['1']->update([
        'average_rating' => 4.5,
    ]));
    echo "--------Update Single Document-------\n";
    echo "\n";
    echo "--------Fetch Single Document-------\n";
    print_r($client->collections['books']->documents['1']->retrieve());
    echo "--------Fetch Single Document-------\n";
    echo "\n";
    echo "--------Search Document-------\n";
    print_r(
        $client->collections['books']->documents->search(
            [
                'q' => 'hunger',
                'query_by' => 'title',
                'sort_by' => 'ratings_count:desc',
            ]
        )
    );
    echo "--------Search Document-------\n";
    echo "\n";
    echo "--------Delete Document-------\n";
    print_r($client->collections['books']->documents['1']->delete());
    echo "--------Delete Document-------\n";
    echo "\n";
    echo "--------Import Documents-------\n";
    $docsToImport         = [];
    $exportedDocStrsArray = explode('\n', $exportedDocStrs);
    foreach ($exportedDocStrsArray as $exportedDocStr) {
        $docsToImport[] = json_decode($exportedDocStr, true);
    }
    $importRes =
        $client->collections['books']->documents->import($docsToImport);
    print_r($importRes);

    // Or if you have documents in JSONL format, and want to save the overhead of parsing JSON,
    // you can also pass in a JSONL string of documents
    // $client->collections['books']->documents->import($exportedDocStrsArray);
    echo "--------Import Documents-------\n";
    echo "\n";
    echo "--------Upsert Documents-------\n";
    $upsertRes =
        $client->collections['books']->documents->import($docsToImport, [
            'action' => 'upsert'
        ]);
    print_r($upsertRes);
    echo "--------Upsert Documents-------\n";
    echo "\n";
    echo "--------Update Documents-------\n";
    $upsertRes =
        $client->collections['books']->documents->import($docsToImport, [
            'action' => 'update'
        ]);
    print_r($upsertRes);
    echo "--------Upsert Documents-------\n";
    echo "\n";
    echo "--------Bulk Delete Documents-------\n";
    print_r($client->collections['books']->documents->delete(['filter_by' => 'publication_year:=2008']));
    echo "--------Bulk Delete Documents-------\n";
    echo "\n";
    echo "--------Delete Collection-------\n";
    print_r($client->collections['books']->delete());
    echo "--------Delete Collection-------\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
