<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProcessCarEventsTest extends TestCase
{
    /**
     * @dataProvider eventsDataProvider
     */
    public function testProcessCarEvents(string $filePath, string $expectedOutputPath): void
    {
        $filePath = __DIR__ . $filePath;
        $expectedOutput = file_get_contents(__DIR__ . $expectedOutputPath);

        $this->withoutMockingConsoleOutput()->artisan('app:process-car-events', ['filePath' => $filePath]);
        $result = Artisan::output();
        $this->assertEquals($expectedOutput, $result);
    }

    public static function eventsDataProvider(): array
    {
        return [
            ['/fixtures/230913_011.csv', '/fixtures/230913_011.txt'],
            ['/fixtures/230913_003.csv', '/fixtures/230913_003.txt'],
            ['/fixtures/1.csv', '/fixtures/1.txt'],
            ['/fixtures/2.csv', '/fixtures/2.txt'],
            ['/fixtures/3.csv', '/fixtures/3.txt'],
        ];
    }
}
