<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\UnauthorizedException;

test('it must set a valid api key when calling API', function () {
    $origin = $this->typesense->http->config['apiKey'];

    $this->typesense->setApiKey($this->slug());

    expect(fn () => $this->typesense->collection->list())
        ->toThrow(UnauthorizedException::class);

    $this->typesense->setApiKey($origin);
});
