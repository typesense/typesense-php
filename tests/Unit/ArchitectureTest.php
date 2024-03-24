<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\TypesenseException;

test('there are no debugging statements remaining in the code')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not()
    ->toBeUsed();

test('strict typing must be enforced in the code')
    ->expect('Typesense')
    ->toUseStrictTypes();

test('the code should not utilize the "final" keyword')
    ->expect('Typesense')
    ->not()
    ->toBeFinal();

test('all exception classes should extend "TypesenseException"')
    ->expect('Typesense\Exceptions')
    ->classes()
    ->toExtend(TypesenseException::class);
