<?php

namespace App\Repositories;

use App\Airport;
use Illuminate\Support\Facades\DB;

/**
 * Class AirportRepository
 * @package App\Repositories
 */
class AirportRepository
{
    /**
     * @param $latitude
     * @param $longitude
     * @param $distanceMax
     * @param $limit
     * @return mixed
     */
    public function closestTo($latitude, $longitude, $distanceMax = 50, $limit = 5)
    {
        return Airport::query()
            ->select(DB::raw(
                'airports.id as id,
                airports.name as name,
                airports.latitude as latitude,
                airports.longitude as longitude,
                aircrafts.id as aircraft_id,
                aircrafts.name as aircraft_name,
                aircrafts.speed as aircraft_speed,
                aircrafts.hourly_cost as aircraft_hourly_cost,
                (6371 * acos (cos(radians(' . $latitude . '))
                * cos(radians( airports.latitude))
                * cos(radians( airports.longitude) - radians(' . $longitude . '))
                + sin(radians(' . $latitude . '))
                * sin(radians( airports.latitude))
                )) as distance'))
            ->from('airports')
            ->having('distance', '<', $distanceMax)
            ->join('aircrafts', 'aircrafts.airport_id', '=', 'airports.id')
            ->take($limit)
            ->get();
    }
}