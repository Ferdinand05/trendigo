<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ShippingController extends Controller
{
    public function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        return  $response->json();
    }

    public function getCities($province_id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/city/' . $province_id);

        return $response->json();
    }

    public function getDistricts($city_id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/district/' . $city_id);

        return $response->json();
    }

    public function getSubDistricts($district_id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/sub-district/' . $district_id);

        return $response->json();
    }

    public function calculateDomesticCost(Request $request)
    {

        $request->validate([

            'destination' => ['required'],
            'weight' => ['required', 'numeric'],
            'courier' => ['required']
        ]);

        $response = Http::asForm()->withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
            'Accept' => 'application/json',
        ])->post('https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost', [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ]);


        return $response->json();
    }



    public function saveAddress(Request $request)
    {


        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'recipient_name' => ['required', 'string'],
            'phone' => ['required', 'max:13'],
            'address' => ['required', 'string', 'max:150'],
            'province' => ['required'],
            'province_id' => ['required'],
            'city' => ['required'],
            'city_id' => ['required'],
            'district' => ['required'],
            'district_id' => ['required'],
            'sub_district' => ['required'],
            'sub_district_id' => ['required'],
            'postal_code' => ['required', 'string']
        ]);

        // Update atau buat alamat baru
        $data = ShippingAddress::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );


        return Session::flash('message', 'Alamat berhasil disimpan');
    }
}
