<?php

namespace Tests\Unit;

use App\Services\CsvService;
use PHPUnit\Framework\TestCase;

class CsvServiceTest extends TestCase
{
    /**
     * @dataProvider fieldValueProvider
     */
    public function testGetFieldValue(string $input, mixed $expected): void
    {
        $result = CsvService::getFieldValue($input);
        $this->assertSame($expected, $result);
    }

    /**
     * Provides various input scenarios for testing getFieldValue.
     *
     * @return array
     */
    public static function fieldValueProvider(): array
    {
        return [
            ['true', true],
            ['TRUE', true],
            ['false', false],
            ['FALSE', false],
            ['123', 123.0],
            ['123.45', 123.45],
            ['0', 0.0],
            ['  true  ', true],
            ['  false  ', false],
            ['  123  ', 123.0],
            ['  123.45  ', 123.45],
            ['"true"', true],
            ['"false"', false],
            ['"123"', 123.0],
            ['"123.45"', 123.45],
            ['"test"', 'test'],
            ['test', 'test'],
            ['', ''],
            ['   ', ''],
        ];
    }
}
