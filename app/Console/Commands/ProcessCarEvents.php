<?php

namespace App\Console\Commands;

use App\Exceptions\CarException;
use App\Services\CarService;
use App\Services\CsvService;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class ProcessCarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-car-events {filePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for processing Car events and returns the status after the last event';

    /**
     * @param CarService $carService
     */
    public function __construct(private readonly CarService $carService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws Throwable
     */
    public function handle(): void
    {
        $filePath = $this->argument('filePath');

        try {
            $file = fopen($filePath, 'r');
        } catch (Exception $exception) {
            $this->fail('An error occurred while opening the file: ' . $exception->getMessage());
        }

        while (($row = fgetcsv($file)) !== false) {
            try {
                $this->carService->process($row[0], CsvService::getFieldValue($row[1]));
            } catch (CarException $e) {
                $this->warn($e->getMessage());
            }
        }

        $this->info($this->carService->getStatus());

        fclose($file);
    }
}
