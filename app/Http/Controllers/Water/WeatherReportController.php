<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use App\Services\CurrentTenant;
use App\Services\TomorrowWeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherReportController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant, TomorrowWeatherService $weather)
    {
        $tenant = $currentTenant->model();
        $user   = $request->user();

        if ($tenant?->lat && $tenant?->lng) {
            // 1st priority: tenant has GPS coordinates set
            $forecast     = $weather->forecast($tenant->lat, $tenant->lng);
            $locationName = $tenant->location_name ?? "{$tenant->lat},{$tenant->lng}";
            $hasLocation  = true;
        } elseif ($user?->zip_code && $user?->country) {
            // 2nd priority: zip + country → geocode to lat/lng via Nominatim
            // (Tomorrow.io free tier only geocodes US zips natively)
            $coords = $this->geocodeZip($user->zip_code, $user->country);
            if ($coords) {
                $forecast = $weather->forecast($coords['lat'], $coords['lng']);
            } else {
                // Nominatim failed — try passing the string directly as fallback
                $forecast = $weather->forecastByLocation("{$user->zip_code}, {$user->country}");
            }
            $locationName = "{$user->zip_code}, {$user->country}";
            $hasLocation  = true;
        } elseif ($user?->zip_code) {
            // 3rd priority: zip code only (works for US zips)
            $forecast     = $weather->forecastByLocation($user->zip_code);
            $locationName = $user->zip_code;
            $hasLocation  = true;
        } else {
            // Final fallback: Athens, Greece
            $forecast     = $weather->forecast(37.9838, 23.7275);
            $locationName = 'Athens, Greece (default)';
            $hasLocation  = false;
        }

        return view('water.weather.index', [
            'forecast'     => $forecast,
            'locationName' => $locationName,
            'hasLocation'  => $hasLocation,
        ]);
    }

    /**
     * Convert a postal code + country name to lat/lng using OpenStreetMap Nominatim.
     * Results are cached for 7 days (location rarely changes).
     *
     * @return array{lat: float, lng: float}|null
     */
    private function geocodeZip(string $zip, string $country): ?array
    {
        $cacheKey = 'geocode_zip_' . md5("{$zip}|{$country}");

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($zip, $country) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'AgroFlux/1.0 (agricultural management)'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'postalcode' => $zip,
                        'country'    => $country,
                        'format'     => 'json',
                        'limit'      => 1,
                    ]);

                $results = $response->json();

                if (!empty($results[0]['lat']) && !empty($results[0]['lon'])) {
                    return [
                        'lat' => (float) $results[0]['lat'],
                        'lng' => (float) $results[0]['lon'],
                    ];
                }

                Log::warning('Nominatim: no results for zip+country', [
                    'zip'     => $zip,
                    'country' => $country,
                    'response'=> $results,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Nominatim geocoding failed', [
                    'zip'     => $zip,
                    'country' => $country,
                    'error'   => $e->getMessage(),
                ]);
            }

            return null;
        });
    }
}
