<?php

namespace App\Http\Controllers;

use App\Car;
use App\Fuel;
use Illuminate\Http\Request;

class FuelsApi extends Controller
{
    public function cars() {
        $allCars = Car::all();

        return response()->json(
            [
                'cars' => $allCars,
            ],
            200
        );
    }
    public function fuels() {
        $allFuels = Fuel::all();

        return response()->json(
            [
                'fuels' => $allFuels,
            ],
            200
        );
    }
    public function distance(Request $request) {
        $searchParams = $request->all();
        $cityStart = $searchParams['cityStart'];
        $cityEnd = $searchParams['cityEnd'];
        $addressStart = $this->get_coordinates($cityStart);
        $addressEnd = $this->get_coordinates($cityEnd);

        if ( !$addressStart || !$addressEnd )
        {
            return response()->json(
                [
                    'isErrorDistance' => 'Wrong address',
                    'dist' => ''
                ],
                200
            );
        } else {
            $dist = $this->GetDrivingDistance($addressStart['lat'], $addressEnd['lat'], $addressStart['long'], $addressEnd['long']);
            return response()->json(
                [
                    'isErrorDistance' => $dist === '' ? 'Wrong address' : '',
                    'dist' => $dist
                ],
                200
            );

        }
    }

    public function carFuelInfo(Request $request) {
        $searchParams = $request->all();
        $fuelId = $searchParams['fuelId'];

        $fuel = Fuel::find($fuelId+1);

        return response()->json(
            [
                'price' => $fuel->kaina
            ],
            200
        );
    }

    function get_coordinates($city)
    {
        $address = urlencode($city);
        $url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Lithuania&key=AIzaSyDWR1IG5TC4NvU568PHmo5UuF4JhTt1JlQ";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);
        $status = $response_a->status;

        if ( $status == 'ZERO_RESULTS' )
        {
            return FALSE;
        }
        else
        {
            $return = array('lat' => $response_a->results[0]->geometry->location->lat, 'long' => $long = $response_a->results[0]->geometry->location->lng);
            return $return;
        }
    }

    function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=lt-LT&key=AIzaSyDWR1IG5TC4NvU568PHmo5UuF4JhTt1JlQ";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        if ($dist = $response_a['rows'][0]['elements'][0]['status'] === 'ZERO_RESULTS') {
            return '';
        } else {
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
        }

        return array('distance' => $dist, 'time' => $time);
    }

}
