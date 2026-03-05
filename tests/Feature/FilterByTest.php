<?php

namespace Feature;

use PHPUnit\Framework\TestCase;
use Typesense\FilterBy;

class FilterByTest extends TestCase
{
    public function testEscapesSpecialCharactersByWrappingInBackticks(): void
    {
        $rawFilterValue = "The 17\" O'Conner && O`Series \n OR a || 1%2 book? (draft), [alpha]";

        $escapedFilterValue = FilterBy::escape($rawFilterValue);

        $this->assertSame(
            "`The 17\" O'Conner && O\\`Series \n OR a || 1%2 book? (draft), [alpha]`",
            $escapedFilterValue
        );
    }

    public function testEscapesMultipleBackticksWithinAFilterString(): void
    {
        $escapedFilterValue = FilterBy::escape('`left` and `right`');

        $this->assertSame('`\\`left\\` and \\`right\\``', $escapedFilterValue);
    }

    public function testEscapeBooleanTrue(): void
    {
        $escapedFilterValue = FilterBy::escape(true);

        $this->assertSame('true', $escapedFilterValue);
    }

    public function testEscapeBooleanFalse(): void
    {
        $escapedFilterValue = FilterBy::escape(false);

        $this->assertSame('false', $escapedFilterValue);
    }

    public function testEscapeFloat(): void
    {
        $escapedFilterValue = FilterBy::escape(1.12);

        $this->assertSame('1.12', $escapedFilterValue);
    }

    public function testEscapeInt(): void
    {
        $escapedFilterValue = FilterBy::escape(1);

        $this->assertSame('1', $escapedFilterValue);
    }
}
