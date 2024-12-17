<?php

namespace Tests\Unit;

use App\Domain\Car;
use App\Enums\EntertainmentEnum;
use App\Exceptions\CarException;
use PHPUnit\Framework\TestCase;

class CarTest extends TestCase
{
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        $this->car = new Car();
    }

    public function testCarDefaults(): void
    {
        $this->assertTrue($this->car->isLocked());
        $this->assertFalse($this->car->isOn());
        $this->assertFalse($this->car->isDriving());
        $this->assertEquals(EntertainmentEnum::Radio->name, $this->car->getEntertainment());
        $this->assertEquals(10, $this->car->getFuelLevel());
        $this->assertEquals(100, $this->car->getLeftWindowRaised());
        $this->assertEquals(100, $this->car->getRightWindowRaised());
        $this->assertEquals(0, $this->car->getOdometerLevel());
    }

    public function testCanLockAndUnlockDoors(): void
    {
        $this->car->setIsLocked(false);
        $this->assertFalse($this->car->isLocked());

        $this->car->setIsLocked(true);
        $this->assertTrue($this->car->isLocked());
    }

    public function testCanTurnCarOnAndOff(): void
    {
        $this->car->setIsOn(true);
        $this->assertTrue($this->car->isOn());

        $this->car->setIsOn(false);
        $this->assertFalse($this->car->isOn());
    }

    public function testCannotDriveWhenCarIsOff(): void
    {
        $this->car->setIsDriving(true);
        $this->assertFalse($this->car->isDriving());
    }

    public function testCanDriveWhenCarIsOnAndHasEnoughFuel(): void
    {
        $this->car->setIsOn(true);
        $this->car->setIsDriving(true);

        $this->assertEquals(7.5, $this->car->getFuelLevel());
        $this->assertEquals(25, $this->car->getOdometerLevel());
    }

    public function testFuelTooLow(): void
    {
        $this->car->setIsOn(true);

        for ($i = 1; $i <= 4; $i++) {
            $this->car->setIsDriving(true);
        }

        $this->assertEquals(0, $this->car->getFuelLevel());
        $this->assertEquals(100, $this->car->getOdometerLevel());
        $this->assertFalse($this->car->isDriving());

        $this->expectException(CarException::class);
        $this->expectExceptionMessage('Fuel level too low!');
        $this->car->setIsDriving(true);
    }

    public function testFuelCannotBeAddedWhileCarIsOn(): void
    {
        $this->car->setIsOn(true);
        $this->car->setIsDriving(true);
        $this->expectException(CarException::class);
        $this->expectExceptionMessage('Cannot add fuel while car is on!');
        $this->car->setFuelLevel(22);
    }

    public function testCanAddFuelWhenCarIsOff(): void
    {
        $this->car->setFuelLevel(5);
        $this->assertEquals(15, $this->car->getFuelLevel());
    }

    public function testWindowsCanBeAdjustedWhenCarIsOn(): void
    {
        $this->car->setIsOn(true);

        $this->car->setLeftWindowRaised(false);
        $this->car->setRightWindowRaised(false);

        $this->assertEquals(50, $this->car->getLeftWindowRaised());
        $this->assertEquals(50, $this->car->getRightWindowRaised());
    }

    public function testWindowsCannotBeAdjustedWhenCarIsOff(): void
    {
        $this->car->setLeftWindowRaised(-50);
        $this->car->setRightWindowRaised(-50);

        $this->assertEquals(100, $this->car->getLeftWindowRaised());
        $this->assertEquals(100, $this->car->getRightWindowRaised());
    }

    public function testWindowsCanBeAdjustedUpTo100(): void
    {
        $this->car->setIsOn(true);

        for ($i = 1; $i <= 3; $i++) {
            $this->car->setLeftWindowRaised(true);
            $this->car->setRightWindowRaised(true);
        }

        $this->assertEquals(100, $this->car->getLeftWindowRaised());
        $this->assertEquals(100, $this->car->getRightWindowRaised());
    }

    public function testWindowsCanBeAdjustedDownTo0(): void
    {
        $this->car->setIsOn(true);

        for ($i = 1; $i <= 3; $i++) {
            $this->car->setLeftWindowRaised(false);
            $this->car->setRightWindowRaised(false);
        }

        $this->assertEquals(0, $this->car->getLeftWindowRaised());
        $this->assertEquals(0, $this->car->getRightWindowRaised());
    }

    public function testCanChangeEntertainment(): void
    {
        $this->car->setIsOn(true);
        $this->assertEquals(EntertainmentEnum::Radio->name, $this->car->getEntertainment());

        $this->car->setEntertainment(EntertainmentEnum::Radio->name, true);
        $this->assertEquals(EntertainmentEnum::Radio->name, $this->car->getEntertainment());

        $this->car->setEntertainment(EntertainmentEnum::Spotify->name, true);
        $this->assertEquals(EntertainmentEnum::Spotify->name, $this->car->getEntertainment());

        $this->car->setEntertainment(EntertainmentEnum::CD->name, true);
        $this->assertEquals(EntertainmentEnum::CD->name, $this->car->getEntertainment());

        $this->car->setEntertainment(EntertainmentEnum::CD->name, false);
        $this->assertEquals(EntertainmentEnum::CD->name, $this->car->getEntertainment());

        $this->car->setEntertainment(EntertainmentEnum::Spotify->name, false);
        $this->assertEquals(EntertainmentEnum::CD->name, $this->car->getEntertainment());
    }

    public function testCannotChangeEntertainmentWhenCarIsOff(): void
    {
        $this->car->setEntertainment(EntertainmentEnum::Spotify->name, true);
        $this->assertEquals(EntertainmentEnum::Radio->name, $this->car->getEntertainment());
    }
}

