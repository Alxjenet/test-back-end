<?php

namespace App\Services;

use App\Airport as AirportModel;
use App\Airport;
use App\Repositories\AirportRepository;
use Illuminate\Http\JsonResponse;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;

/**
 * Class SearchService
 */
class SearchService
{
    /**
     * @var array
     */
    protected $searchResult = [];

    /**
     * @var AirportRepository
     */
    private $airportRepository;

    /**
     * @var Geotools
     */
    private $geoTools;

    /**
     * SearchService constructor.
     * @param AirportRepository $airportRepository
     * @param Geotools $geoTools
     */
    public function __construct(AirportRepository $airportRepository, Geotools $geoTools)
    {
        $this->airportRepository = $airportRepository;
        $this->geoTools = $geoTools;
    }

    /**
     * @param $coordinate
     * @return array
     */
    protected function extractCoordinate($coordinate)
    {
        $point = [];
        $data = explode(',', $coordinate);
        $point['latitude'] = $data[0];
        $point['longitude'] = $data[1];

        return $point;
    }

    /**
     * Compute the Time
     *
     * @param AirportModel $airportModel
     * @param string $arrival
     * @param int $round
     * @return float
     */
    protected function computeTime(AirportModel $airportModel, $arrival, $round = 2)
    {
        $from = new Coordinate([$airportModel->getLatitude(), $airportModel->getLongitude()]);
        $to = new Coordinate($arrival);

        return round($this->getDistanceBetweenTwoPoints($from, $to) / $airportModel->aircraft_speed, $round);
    }

    /**
     * Compute the cost
     *
     * @param int $time
     * @param int$hourlyCost
     * @return int
     */
    protected function computeCost($time, $hourlyCost)
    {
        return $time * $hourlyCost;
    }

    /**
     * @param string $from
     * @param string $to
     * @return JsonResponse
     */
    public function getAircrafts($from, $to)
    {
        // Extract data string to array
        $fromPoint = $this->extractCoordinate($from);
        $toPoint = $this->extractCoordinate($to);

        /** @var AirportModel[] $airportModels */
        $airportModels = $this->getDetailForAllAirportsAroundAPoint($fromPoint['latitude'], $fromPoint['longitude']);
        $arrivalAirport = array_first($this->getDetailForOneAirportsAroundAPoint($toPoint['latitude'], $toPoint['longitude']));

        /** @var AirportModel $airportModel */
        foreach ($airportModels as $key => $airportModel) {
            $this->searchResult['data'][$key] = $this->buildSearchResult($airportModel, $arrivalAirport, $to);
        }

        // In case of any result are found return a message with status code 200
        if (!count($airportModels)) {
            return new JsonResponse(['message' => 'There is no aircraft found with your criteria.'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse($this->searchResult, JsonResponse::HTTP_OK);
    }

    /**
     * Prepare the search result before the json_code
     *
     * @param Airport $airportModel
     * @param Airport $arrivalAirport
     * @param $to
     * @return mixed
     */
    protected function buildSearchResult(Airport $airportModel, Airport $arrivalAirport, $to) :array
    {
        $time = $this->computeTime($airportModel, $to);
        $cost = $this->computeCost($time, $airportModel->aircraft_hourly_cost);

        $searchResult['id'] = $airportModel->aircraft_id;
        $searchResult['name'] = $airportModel->aircraft_name;
        $searchResult['time'] = $time;
        $searchResult['cost'] = $cost;
        $searchResult['departure_airport'] = $airportModel->getName();
        $searchResult['arrival_airport'] = $arrivalAirport->getName();

        return $searchResult;
    }

    /**
     * Compute the distance between two points
     * In km by default
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @param string $unit
     * @param int $round
     * @return float
     */
    public function getDistanceBetweenTwoPoints(Coordinate $from, Coordinate $to, $unit = 'km', $round = 1)
    {
        return round($this->geoTools->distance()->in($unit)->setFrom($from)->setTo($to)->greatCircle(), $round);
    }

    /**
     * Find Airports around a specific point
     * To 50km by default
     *
     * @param string $latitude
     * @param string $longitude
     * @return mixed
     */
    public function getDetailForAllAirportsAroundAPoint($latitude, $longitude)
    {
        return $this->airportRepository->closestTo($latitude, $longitude);
    }
    /**
     * Find Airports the nearest of the point
     *
     * @param string $latitude
     * @param string $longitude
     * @return mixed
     */
    public function getDetailForOneAirportsAroundAPoint($latitude, $longitude)
    {
        return $this->airportRepository->closestTo($latitude, $longitude, 1000, 1);
    }
}