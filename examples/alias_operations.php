<?php /** @noinspection ForgottenDebugOutputInspection */

include '../vendor/autoload.php';

use Devloops\Typesence\Client;

try {
    $client = new Client(
      [
        'api_key'         => 'abcd',
        'nodes'           => [
          [
            'host'     => 'localhost',
            'port'     => '8108',
            'protocol' => 'http',
          ],
        ],
        'connection_timeout_seconds' => 2,
      ]
    );
    echo '<pre>';
    echo "--------Create Collection-------\n";
    print_r(
      $client->collections->create(
        [
          'name'                  => 'books_january',
          'fields'                => [
            [
              'name' => 'title',
              'type' => 'string',
            ],
            [
              'name' => 'authors',
              'type' => 'string[]',
            ],
            [
              'name'  => 'authors_facet',
              'type'  => 'string[]',
              'facet' => true,
            ],
            [
              'name' => 'publication_year',
              'type' => 'int32',
            ],
            [
              'name'  => 'publication_year_facet',
              'type'  => 'string',
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
    echo "--------Create Collection Alias-------\n";
    print_r(
      $client->aliases->upsert(
        'books',
        [
          'collection_name' => 'books_january',
        ]
      )
    );
    echo "--------Create Collection Alias-------\n";
    echo "\n";
    echo "--------Create Document on Alias-------\n";
    print_r(
      $client->collections['books']->documents->create(
        [
          'id'                        => '1',
          'original_publication_year' => 2008,
          'authors'                   => [
            'Suzanne Collins',
          ],
          'average_rating'            => 4.34,
          'publication_year'          => 2008,
          'publication_year_facet'    => '2008',
          'authors_facet'             => [
            'Suzanne Collins',
          ],
          'title'                     => 'The Hunger Games',
          'image_url'                 => 'https://images.gr-assets.com/books/1447303603m/2767052.jpg',
          'ratings_count'             => 4780653,
        ]
      )
    );
    echo "--------Create Document on Alias-------\n";
    echo "\n";
    echo "--------Search Document on Alias-------\n";
    print_r(
      $client->collections['books']->documents->search(
        [
          'q'        => 'hunger',
          'query_by' => 'title',
          'sort_by'  => 'ratings_count:desc',
        ]
      )
    );
    echo "--------Search Document on Alias-------\n";
    echo "\n";
    echo "--------Retrieve All Aliases-------\n";
    print_r($client->aliases->retrieve());
    echo "--------Retrieve All Aliases-------\n";
    echo "\n";
    echo "--------Retrieve All Alias Documents-------\n";
    print_r($client->aliases['books']->retrieve());
    echo "--------Retrieve All Alias Documents-------\n";
    echo "\n";
    echo "--------Delete Alias-------\n";
    print_r($client->aliases['books']->delete());
    echo "--------Delete Alias-------\n";
    echo "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
