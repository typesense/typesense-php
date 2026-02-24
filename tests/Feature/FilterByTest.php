<?php

namespace Feature;

use PHPUnit\Framework\TestCase;
use Typesense\FilterBy;

class FilterByTest extends TestCase
{
    public function testEscapesSpecialCharactersByWrappingInBackticks(): void
    {
        $rawFilterValue = "The 17\" O'Conner && O`Series \n OR a || 1%2 book? (draft), [alpha]";

        $escapedFilterValue = FilterBy::escapeString($rawFilterValue);

        $this->assertSame(
            "`The 17\" O'Conner && O\\`Series \n OR a || 1%2 book? (draft), [alpha]`",
            $escapedFilterValue
        );
    }

    public function testEscapesMultipleBackticksWithinAFilterString(): void
    {
        $escapedFilterValue = FilterBy::escapeString('`left` and `right`');

        $this->assertSame('`\\`left\\` and \\`right\\``', $escapedFilterValue);
    }
}
