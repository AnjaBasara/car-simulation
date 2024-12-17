<?php

namespace App\Domain;

use App\Enums\EntertainmentEnum;
use App\Exceptions\CarException;

class Car
{
    const MAX_FUEL_CAPACITY = 50;
    const MAX_WINDOW_RAISED = 100;
    const WINDOW_RAISE_INCREMENT = 50;
    const FUEL_REQUIRED_PER_DRIVE = 2.5;
    const KM_PER_DRIVE = 25;

    private bool $isLocked = true;
    private bool $isOn = false;
    private bool $isDriving = false;
    private bool $isEntertainmentOn = false;
    private string $entertainment = EntertainmentEnum::Radio->name;
    private float $fuelLevel = 10;
    private int $leftWindowRaised = 100;
    private int $rightWindowRaised = 100;
    private int $odometerLevel = 0;

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @param bool $isLocked
     */
    public function setIsLocked(bool $isLocked): void
    {
        $this->isLocked = $isLocked;
    }

    /**
     * @return bool
     */
    public function isOn(): bool
    {
        return $this->isOn;
    }

    /**
     * @param bool $isOn
     */
    public function setIsOn(bool $isOn): void
    {
        $this->isOn = $isOn;
        $this->isEntertainmentOn = $isOn;
    }

    /**
     * @return bool
     */
    public function isDriving(): bool
    {
        return $this->isDriving;
    }

    /**
     * @param bool $isDriving
     * @throws CarException
     */
    public function setIsDriving(bool $isDriving): void
    {
        if (!$this->isOn) {
            return;
        }

        if ($isDriving) {
            if ($this->fuelLevel < self::FUEL_REQUIRED_PER_DRIVE) {
                throw new CarException('Fuel level too low!');
            }

            $this->isDriving = true;
            $this->setFuelLevel(-self::FUEL_REQUIRED_PER_DRIVE);
            $this->setOdometerLevel(self::KM_PER_DRIVE);
        } else {
            $this->isDriving = false;
        }
    }

    public function isEntertainmentOn(): bool
    {
        return $this->isEntertainmentOn;
    }

    /**
     * @return string
     */
    public function getEntertainment(): string
    {
        return $this->entertainment;
    }

    /**
     * @param string $entertainment
     * @param bool $isEntertainmentOn
     */
    public function setEntertainment(string $entertainment, bool $isEntertainmentOn): void
    {
        if (!$this->isOn) {
            return;
        }

        if ($this->entertainment === $entertainment) {
            $this->isEntertainmentOn = $isEntertainmentOn;
        } else {
            if ($isEntertainmentOn) {
                $this->isEntertainmentOn = true;
                $this->entertainment = $entertainment;
            } else {
                return; // disregard
            }
        }
    }

    /**
     * @return float
     */
    public function getFuelLevel(): float
    {
        return $this->fuelLevel;
    }

    /**
     * @param float $fuelLevel
     * @throws CarException
     */
    public function setFuelLevel(float $fuelLevel): void
    {
        if ($this->isOn && $fuelLevel > 0) {
            throw new CarException('Cannot add fuel while car is on!');
        }

        $this->fuelLevel = $this->getNewValue(
            $this->fuelLevel,
            $fuelLevel,
            self::MAX_FUEL_CAPACITY
        );

        if ($this->fuelLevel == 0) {
            $this->setIsDriving(false);
        }
    }

    /**
     * @return int
     */
    public function getLeftWindowRaised(): int
    {
        return $this->leftWindowRaised;
    }

    /**
     * @param bool $isRaised
     */
    public function setLeftWindowRaised(bool $isRaised): void
    {
        $this->operateWindow($this->leftWindowRaised, $isRaised);
    }

    /**
     * @return int
     */
    public function getRightWindowRaised(): int
    {
        return $this->rightWindowRaised;
    }

    /**
     * @param bool $isRaised
     */
    public function setRightWindowRaised(bool $isRaised): void
    {
        $this->operateWindow($this->rightWindowRaised, $isRaised);
    }

    /**
     * @return int
     */
    public function getOdometerLevel(): int
    {
        return $this->odometerLevel;
    }

    /**
     * @param int $odometerLevel
     * @return void
     */
    private function setOdometerLevel(int $odometerLevel): void
    {
        $this->odometerLevel += $odometerLevel;
    }

    /**
     * @param int $window window being operated
     * @param bool $isRaised true if raised, otherwise lowered
     * @return void
     */
    private function operateWindow(int &$window, bool $isRaised): void
    {
        if (!$this->isOn) {
            return;
        }

        $window = $this->getNewValue(
            $window,
            $isRaised ? Car::WINDOW_RAISE_INCREMENT : -Car::WINDOW_RAISE_INCREMENT,
            self::MAX_WINDOW_RAISED
        );
    }

    /**
     * @param float $currentValue
     * @param float $change
     * @param int $maxValue
     * @return float
     */
    private function getNewValue(float $currentValue, float $change, int $maxValue): float
    {
        $newValue = $currentValue + $change;

        if ($newValue > $maxValue) {
            $newValue = $maxValue;
        } else if ($newValue < 0) {
            $newValue = 0;
        }

        return $newValue;
    }

}
