<?php

namespace Modules\Drivisa\Services;

class Geocoder
{
    protected function getAddress($lat, $long)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&key=" . env('GOOGLE_MAP_KEY');

        $json = $this->curl($url);

        $details = json_decode($json, TRUE);

        if ($details['status'] === 'OK') {
            return $details['results'][0]['formatted_address'];
        }

        return "Unknown";
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static())->$name(...$arguments);
    }

    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}