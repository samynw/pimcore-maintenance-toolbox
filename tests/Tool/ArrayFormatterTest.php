<?php

namespace MaintenanceToolboxBundle\Tests\Tool;

use MaintenanceToolboxBundle\Tool\ArrayFormatter;
use PHPUnit\Framework\TestCase;

class ArrayFormatterTest extends TestCase
{
    private function getFlatArray(): array
    {
        return [
            'a__b__x' => 'foo',
            'a__b__y' => 'bar',
            'a__c' => 'baz',
        ];
    }

    private function getNestedArray(): array
    {
        return [
            'a' => [
                'b' => [
                    'x' => 'foo',
                    'y' => 'bar',
                ],
                'c' => 'baz',
            ]
        ];
    }

    public function testArrayCanBeFlattened()
    {
        $formatter = new ArrayFormatter();
        $result = $formatter->toFlatArray($this->getNestedArray());
        self::assertIsArray($result);
        self::assertCount(3, $result);
        self::assertEquals($this->getFlatArray(), $result);
    }

    public function testArrayCanBeNested()
    {
        $formatter = new ArrayFormatter();
        $result = $formatter->toNestedArray($this->getFlatArray());
        self::assertIsArray($result);
        // Only one child is present, the rest is nested
        self::assertCount(1, $result);
        self::assertEquals($this->getNestedArray(), $result);
    }
}
