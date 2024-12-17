<?php

namespace Tests\Unit;

use App\Domain\Car;
use App\Enums\EntertainmentEnum;
use App\Exceptions\CarException;
use App\Services\CarService;
use PHPUnit\Framework\TestCase;

class CarServiceTest extends TestCase
{
    private Car $carMock;
    private CarService $carService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carMock = $this->createMock(Car::class);
        $this->carService = new CarService($this->carMock);
    }

    public function testDriverUnlocksDoors(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsLocked')
            ->with(false);

        $this->carService->process('driver-unlocks-doors', true);
    }

    public function testDriverUnlocksDoorsFalse(): void
    {
        $this->carMock->expects($this->never())
            ->method('setIsLocked');

        $this->carService->process('driver-unlocks-doors', false);
    }

    public function testDriverLocksDoors(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsLocked')
            ->with(true);

        $this->carService->process('driver-locks-doors', true);
    }

    public function testDriverLocksDoorsFalse(): void
    {
        $this->carMock->expects($this->never())
            ->method('setIsLocked');

        $this->carService->process('driver-locks-doors', false);
    }

    public function testDriverTurnsCarOn(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsOn')
            ->with(true);

        $this->carService->process('driver-turns-car-on', true);
    }

    public function testDriverTurnsCarOnFalse(): void
    {
        $this->carMock->expects($this->never())
            ->method('setIsOn');

        $this->carService->process('driver-turns-car-on', false);
    }

    public function testDriverTurnsCarOff(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsOn')
            ->with(false);

        $this->carService->process('driver-turns-car-off', true);
    }

    public function testDriverTurnsCarOffFalse(): void
    {
        $this->carMock->expects($this->never())
            ->method('setIsOn');

        $this->carService->process('driver-turns-car-off', false);
    }

    public function testDriverListensToRadio(): void
    {
        $this->carMock->expects($this->once())
            ->method('setEntertainment')
            ->with(EntertainmentEnum::Radio->name, true);

        $this->carService->process('driver-listen-radio', true);
    }

    public function testDriverListensToCd(): void
    {
        $this->carMock->expects($this->once())
            ->method('setEntertainment')
            ->with(EntertainmentEnum::CD->name, true);

        $this->carService->process('driver-listen-cd', true);
    }

    public function testDriverListensToSpotify(): void
    {
        $this->carMock->expects($this->once())
            ->method('setEntertainment')
            ->with(EntertainmentEnum::Spotify->name, true);

        $this->carService->process('driver-listen-spotify', true);
    }

    public function testAddFuel(): void
    {
        $fuelToAdd = 0.5; // 50% of the tank
        $this->carMock->expects($this->once())
            ->method('setFuelLevel')
            ->with($fuelToAdd * Car::MAX_FUEL_CAPACITY);

        $this->carService->process('add-fuel', $fuelToAdd);
    }

    public function testAddFuelInvalid(): void
    {
        $this->carMock->expects($this->never())
            ->method('setFuelLevel');

        $this->carService->process('add-fuel', 'test');
    }

    public function testRaiseLeftWindow(): void
    {
        $this->carMock->expects($this->once())
            ->method('setLeftWindowRaised')
            ->with(true);

        $this->carService->process('driver-raises-windows', 'left');
    }

    public function testRaiseRightWindow(): void
    {
        $this->carMock->expects($this->once())
            ->method('setRightWindowRaised')
            ->with(true);

        $this->carService->process('driver-raises-windows', 'right');
    }

    public function testLowerLeftWindow(): void
    {
        $this->carMock->expects($this->once())
            ->method('setLeftWindowRaised')
            ->with(false);

        $this->carService->process('driver-lowers-windows', 'left');
    }

    public function testLowerRightWindow(): void
    {
        $this->carMock->expects($this->once())
            ->method('setRightWindowRaised')
            ->with(false);

        $this->carService->process('driver-lowers-windows', 'right');
    }

    public function testDrive(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsDriving')
            ->with(true);

        $this->carService->process('drive', 'drive');
    }

    public function testStop(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsDriving')
            ->with(false);

        $this->carService->process('drive', 'stop');
    }

    public function testDriveThrowsExceptionIfFuelLow(): void
    {
        $this->carMock->expects($this->once())
            ->method('setIsDriving')
            ->with(true)
            ->willThrowException(new CarException('Fuel level too low!'));

        $this->expectException(CarException::class);
        $this->expectExceptionMessage('Fuel level too low!');

        $this->carService->process('drive', 'drive');
    }

    public function testInvalidEvent(): void
    {
        $this->carMock->expects($this->never())
            ->method($this->anything());

        $this->carService->process('test', 'test');
    }

    public function testGetStatus(): void
    {
        $this->carMock->method('isLocked')->willReturn(true);
        $this->carMock->method('isOn')->willReturn(false);
        $this->carMock->method('getEntertainment')->willReturn(EntertainmentEnum::Radio->name);
        $this->carMock->method('getFuelLevel')->willReturn(25.0);
        $this->carMock->method('getLeftWindowRaised')->willReturn(100);
        $this->carMock->method('getRightWindowRaised')->willReturn(100);
        $this->carMock->method('getOdometerLevel')->willReturn(500);

        $expectedStatus = file_get_contents(__DIR__ . '/fixtures/status.txt');

        $this->assertEquals($expectedStatus, $this->carService->getStatus());
    }
}
