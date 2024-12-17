<?php

namespace App\Services;

use App\Domain\Car;
use App\Enums\EntertainmentEnum;
use App\Exceptions\CarException;

class CarService
{
    public function __construct(private readonly Car $car)
    {
    }

    /**
     * @throws CarException
     */
    public function process(string $event, mixed $value): void
    {
        switch ($event) {
            case 'driver-unlocks-doors':
                if ($value === false) {
                    break;
                }
                $this->car->setIsLocked(false);
                break;
            case 'driver-locks-doors':
                if ($value === false) {
                    break;
                }
                $this->car->setIsLocked(true);
                break;
            case 'driver-turns-car-on':
                if ($value === false) {
                    break;
                }
                $this->car->setIsOn(true);
                break;
            case 'driver-turns-car-off':
                if ($value === false) {
                    break;
                }
                $this->car->setIsOn(false);
                break;
            case 'driver-listen-radio':
                $this->car->setEntertainment(EntertainmentEnum::Radio->name, $value);
                break;
            case 'driver-listen-cd':
                $this->car->setEntertainment(EntertainmentEnum::CD->name, $value);
                break;
            case 'driver-listen-spotify':
                if ($value === false) {
                    return;
                }
                $this->car->setEntertainment(EntertainmentEnum::Spotify->name, $value);
                break;
            case 'add-fuel':
                if (!is_float($value)) {
                    break;
                }
                $this->car->setFuelLevel($value * Car::MAX_FUEL_CAPACITY);
                break;
            case 'driver-raises-windows':
                if ($value === 'left') {
                    $this->car->setLeftWindowRaised(true);
                } else if ($value === 'right') {
                    $this->car->setRightWindowRaised(true);
                }
                break;
            case 'driver-lowers-windows':
                if ($value === 'left') {
                    $this->car->setLeftWindowRaised(false);
                } else if ($value === 'right') {
                    $this->car->setRightWindowRaised(false);
                }
                break;
            case 'drive':
                if ($value === 'drive') {
                    $this->car->setIsDriving(true);
                } else if ($value === 'stop') {
                    $this->car->setIsDriving(false);
                }
                break;
            default:
                break;
        }
    }

    public function getStatus(): string
    {
        return sprintf(
            "Car status:\n- Doors: %s\n- Car engine: %s\n- Entertainment: %s\n- Fuel level: %d%%\n- Windows raised: Left - %d%%, Right - %d%%\n- Odometer: %dkm\n",
            $this->car->isLocked() ? 'Locked' : 'Unlocked',
            $this->car->isOn() ? 'On' : 'Off',
            ($this->car->isEntertainmentOn() ? 'On' : 'Off') . ' (' . $this->car->getEntertainment() .')',
            ($this->car->getFuelLevel() / Car::MAX_FUEL_CAPACITY) * 100,
            $this->car->getLeftWindowRaised(),
            $this->car->getRightWindowRaised(),
            $this->car->getOdometerLevel()
        );
    }
}
