<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

test('it will throw error', function () {
    $origin = $this->typesense->http->config['url'];

    $this->typesense->setUrl('http://0.0.0.0:1');

    expect(
        fn () => $this->typesense->collection->retrieve('testing'),
    )
        ->toThrow('0.0.0.0');

    $this->typesense->setUrl($origin);
});
