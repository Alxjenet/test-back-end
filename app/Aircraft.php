<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Aircraft
 * @package App
 *
 * @property int airport_id
 * @property string name
 * @property int speed
 * @property int hourly_cost
 * @method Illuminate\Database\Eloquent\Builder| \App\Aircraft
 */
class Aircraft extends Model
{
    /**
     * @return int
     */
    public function getAirportId()
    {
        return $this->airport_id;
    }

    /**
     * @param int $airport_id
     * @return Aircraft
     */
    public function setAirportId($airport_id)
    {
        $this->airport_id = $airport_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @return int
     */
    public function getHourlyCost()
    {
        return $this->hourly_cost;
    }

    /**
     * @param int $hourly_cost
     * @return Aircraft
     */
    public function setHourlyCost($hourly_cost)
    {
        $this->hourly_cost = $hourly_cost;
        return $this;
    }

    /**
     * @param $speed
     * @return $this
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
        return $this;
    }
}
