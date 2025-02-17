<?php

namespace Modules\Drivisa\Services;

class DistanceCalculationService
{
    public $source_lat;
    public $source_lng;
    public $dest_lat;
    public $dest_lng;

    public function __construct()
    {

    }

    public function setLocation($source_lat, $source_lng, $dest_lat, $dest_lng)
    {
        $this->source_lat = $source_lat;
        $this->source_lng = $source_lng;
        $this->dest_lat = $dest_lat;
        $this->dest_lng = $dest_lng;

        return $this;
    }

    public function calculateViaGoogle()
    {
        $origins = $this->source_lat . "," . $this->source_lng;
        $destinations = $this->dest_lat . "," . $this->dest_lng;

        $query = http_build_query([
            'origins' => $origins,
            'destinations' => $destinations,
            'mode' => 'driving',
            'sensor' => 'false',
            'key' => env('GOOGLE_MAP_KEY')
        ]);

        $details = "https://maps.googleapis.com/maps/api/distancematrix/json?" . $query;

        $json = $this->curl($details);

        $details = json_decode($json, TRUE);

        $meter = 0;


        if (count($details['rows']) > 0) {
            foreach ($details['rows'] as $row) {
                $index = 0;
                if ($row['elements'][$index]['status'] != "ZERO_RESULTS") {
                    if ($row['elements'][$index]['distance']['value'] == 0) {
                        $index = 1;
                    }
                    if (isset($row['elements'][$index])) {
                        $meter += $row['elements'][$index]['distance']['value'];
                    }
                }
            }
        }

        return $this->meterToKm($meter);
    }

    public function calculateViaDistance($unit = 'K')
    {

        $lat1 = $this->source_lat;
        $lon1 = $this->source_lng;
        $lat2 = $this->dest_lat;
        $lon2 = $this->dest_lng;

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function calculateViaDistanceUnused($earthRadius = 6371)
    {
        $latitudeFrom = $this->source_lat;
        $longitudeFrom = $this->source_lng;
        $latitudeTo = $this->dest_lat;
        $longitudeTo = $this->dest_lng;

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    private function meterToKm($meter)
    {
        return (int)$meter / 1000;
    }

    function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}