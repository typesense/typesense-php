<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use stdClass;
use Typesense\Tests\Objects\TestObject;

test('it will automatically assign properties', function () {
    $data = new stdClass();

    $data->name = 'Hello';

    $data->description = 'World';

    $object = TestObject::from($data);

    expect($object->name)->toBe($data->name);

    expect($object->description)->toBe($data->description);
});
