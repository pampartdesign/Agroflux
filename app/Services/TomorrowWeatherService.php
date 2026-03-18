<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TomorrowWeatherService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.tomorrow.key', '');
        $this->baseUrl = config('services.tomorrow.base_url', 'https://api.tomorrow.io/v4');
    }

    /**
     * Fetch the full forecast for a lat/lng location.
     * Results are cached for 30 minutes to preserve free-tier rate limits.
     */
    public function forecast(float $lat, float $lng): array
    {
        return $this->forecastByLocation("{$lat},{$lng}");
    }

    /**
     * Fetch the full forecast using a zip code (or any location string Tomorrow.io accepts).
     * Tomorrow.io accepts zip codes, city names, or "lat,lng" strings as the location param.
     */
    public function forecastByZip(string $zip): array
    {
        return $this->forecastByLocation($zip);
    }

    /**
     * Core forecast fetch accepting any Tomorrow.io location string.
     */
    public function forecastByLocation(string $location): array
    {
        $cacheKey = 'tomorrow_forecast_' . md5($location);

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($location) {
            return $this->fetchForecast($location);
        });
    }

    private function fetchForecast(string $location): array
    {
        if (empty($this->apiKey)) {
            return $this->emptyResult('API key not configured.');
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/weather/forecast", [
                'location' => $location,
                'apikey'   => $this->apiKey,
                'units'    => 'metric',
                'fields'   => implode(',', [
                    // Current / hourly
                    'temperature',
                    'temperatureApparent',
                    'humidity',
                    'windSpeed',
                    'windDirection',
                    'windGust',
                    'precipitationProbability',
                    'precipitationIntensity',
                    'rainAccumulation',
                    'snowAccumulation',
                    'uvIndex',
                    'cloudCover',
                    'visibility',
                    'weatherCode',
                    'pressureSurfaceLevel',
                    'dewPoint',
                    // Daily extras
                    'temperatureMax',
                    'temperatureMin',
                    'rainAccumulationSum',
                    'snowAccumulationSum',
                    'sunriseTime',
                    'sunsetTime',
                    'moonriseTime',
                    'moonsetTime',
                    'uvIndexMax',
                    'windSpeedAvg',
                    'windSpeedMax',
                    'precipitationProbabilityAvg',
                    'precipitationProbabilityMax',
                    'soilMoistureVolumetric0To10',
                    'soilTemperature0To10cm',
                    'evapotranspiration',
                ]),
            ]);

            if ($response->failed()) {
                Log::warning('Tomorrow.io API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->emptyResult('Weather service returned an error: ' . $response->status());
            }

            $data = $response->json();

            return [
                'error'   => null,
                'current' => $this->parseCurrently($data),
                'hourly'  => $this->parseHourly($data),
                'daily'   => $this->parseDaily($data),
            ];

        } catch (\Throwable $e) {
            Log::error('Tomorrow.io exception', ['message' => $e->getMessage()]);
            return $this->emptyResult('Could not reach weather service: ' . $e->getMessage());
        }
    }

    // ── Parsers ────────────────────────────────────────────────────────

    private function parseCurrently(array $data): array
    {
        // Use first hourly interval as "current"
        $hourly = data_get($data, 'timelines.hourly.0.values', []);

        return [
            'temp'             => round(data_get($hourly, 'temperature', 0), 1),
            'feels_like'       => round(data_get($hourly, 'temperatureApparent', 0), 1),
            'humidity'         => round(data_get($hourly, 'humidity', 0)),
            'wind_speed'       => round(data_get($hourly, 'windSpeed', 0), 1),
            'wind_dir'         => data_get($hourly, 'windDirection', 0),
            'wind_gust'        => round(data_get($hourly, 'windGust', 0), 1),
            'precip_prob'      => round(data_get($hourly, 'precipitationProbability', 0)),
            'precip_intensity' => round(data_get($hourly, 'precipitationIntensity', 0), 2),
            'uv_index'         => round(data_get($hourly, 'uvIndex', 0), 1),
            'cloud_cover'      => round(data_get($hourly, 'cloudCover', 0)),
            'visibility'       => round(data_get($hourly, 'visibility', 0), 1),
            'pressure'         => round(data_get($hourly, 'pressureSurfaceLevel', 0), 1),
            'dew_point'        => round(data_get($hourly, 'dewPoint', 0), 1),
            'weather_code'     => data_get($hourly, 'weatherCode', 1000),
            'time'             => data_get($data, 'timelines.hourly.0.time'),
        ];
    }

    private function parseHourly(array $data): array
    {
        $raw = data_get($data, 'timelines.hourly', []);
        return collect($raw)->take(24)->map(function ($item) {
            $v = $item['values'] ?? [];
            return [
                'time'        => $item['time'],
                'temp'        => round(data_get($v, 'temperature', 0), 1),
                'feels_like'  => round(data_get($v, 'temperatureApparent', 0), 1),
                'humidity'    => round(data_get($v, 'humidity', 0)),
                'precip_prob' => round(data_get($v, 'precipitationProbability', 0)),
                'precip_mm'   => round(data_get($v, 'rainAccumulation', 0), 1),
                'wind_speed'  => round(data_get($v, 'windSpeed', 0), 1),
                'uv_index'    => round(data_get($v, 'uvIndex', 0), 1),
                'cloud_cover' => round(data_get($v, 'cloudCover', 0)),
                'weather_code'=> data_get($v, 'weatherCode', 1000),
            ];
        })->values()->toArray();
    }

    private function parseDaily(array $data): array
    {
        $raw = data_get($data, 'timelines.daily', []);
        return collect($raw)->take(7)->map(function ($item) {
            $v = $item['values'] ?? [];
            return [
                'time'          => $item['time'],
                'temp_max'      => round(data_get($v, 'temperatureMax', 0), 1),
                'temp_min'      => round(data_get($v, 'temperatureMin', 0), 1),
                'precip_prob'   => round(data_get($v, 'precipitationProbabilityMax', 0)),
                'rain_mm'       => round(data_get($v, 'rainAccumulationSum', 0), 1),
                'snow_mm'       => round(data_get($v, 'snowAccumulationSum', 0), 1),
                'uv_max'        => round(data_get($v, 'uvIndexMax', 0), 1),
                'wind_speed_avg'=> round(data_get($v, 'windSpeedAvg', 0), 1),
                'wind_speed_max'=> round(data_get($v, 'windSpeedMax', 0), 1),
                'sunrise'       => data_get($v, 'sunriseTime'),
                'sunset'        => data_get($v, 'sunsetTime'),
                'weather_code'  => data_get($v, 'weatherCode', 1000),
                // Soil / agri
                'soil_moisture' => round(data_get($v, 'soilMoistureVolumetric0To10', 0), 2),
                'soil_temp'     => round(data_get($v, 'soilTemperature0To10cm', 0), 1),
                'evapotranspiration' => round(data_get($v, 'evapotranspiration', 0), 2),
            ];
        })->values()->toArray();
    }

    // ── Helpers ────────────────────────────────────────────────────────

    private function emptyResult(string $error): array
    {
        return [
            'error'   => $error,
            'current' => null,
            'hourly'  => [],
            'daily'   => [],
        ];
    }

    /**
     * Map Tomorrow.io weather codes to a human label + emoji (locale-aware).
     * Full code list: https://docs.tomorrow.io/reference/data-layers-weather-codes
     */
    public static function weatherLabel(int $code): array
    {
        $emojis = [
            1000 => '☀️',  1100 => '🌤️', 1101 => '⛅',  1102 => '🌥️',
            1001 => '☁️',  2000 => '🌫️', 2100 => '🌫️', 4000 => '🌦️',
            4001 => '🌧️', 4200 => '🌦️', 4201 => '🌧️', 5000 => '❄️',
            5001 => '🌨️', 5100 => '🌨️', 5101 => '❄️',  6000 => '🌧️',
            6001 => '🌧️', 6200 => '🌧️', 6201 => '🌧️', 7000 => '🌨️',
            7101 => '🌨️', 7102 => '🌨️', 8000 => '⛈️',
        ];

        $labels = [
            'en' => [
                1000 => 'Clear',           1100 => 'Mostly Clear',
                1101 => 'Partly Cloudy',   1102 => 'Mostly Cloudy',
                1001 => 'Cloudy',          2000 => 'Fog',
                2100 => 'Light Fog',       4000 => 'Drizzle',
                4001 => 'Rain',            4200 => 'Light Rain',
                4201 => 'Heavy Rain',      5000 => 'Snow',
                5001 => 'Flurries',        5100 => 'Light Snow',
                5101 => 'Heavy Snow',      6000 => 'Freezing Drizzle',
                6001 => 'Freezing Rain',   6200 => 'Light Freezing Rain',
                6201 => 'Heavy Freezing Rain', 7000 => 'Ice Pellets',
                7101 => 'Heavy Ice Pellets',   7102 => 'Light Ice Pellets',
                8000 => 'Thunderstorm',
            ],
            'el' => [
                1000 => 'Αίθριος',          1100 => 'Σχεδόν Αίθριος',
                1101 => 'Μερική Νέφωση',    1102 => 'Κυρίως Συννεφιά',
                1001 => 'Συννεφιά',         2000 => 'Ομίχλη',
                2100 => 'Ελαφρά Ομίχλη',   4000 => 'Ψιχάλα',
                4001 => 'Βροχή',            4200 => 'Ελαφρά Βροχή',
                4201 => 'Έντονη Βροχή',     5000 => 'Χιόνι',
                5001 => 'Χιονοθύελλα',      5100 => 'Ελαφρύ Χιόνι',
                5101 => 'Έντονο Χιόνι',     6000 => 'Παγωμένη Ψιχάλα',
                6001 => 'Παγωμένη Βροχή',   6200 => 'Ελαφρά Παγωμένη Βροχή',
                6201 => 'Έντονη Παγωμένη Βροχή', 7000 => 'Χαλαζόπτωση',
                7101 => 'Έντονη Χαλαζόπτωση',    7102 => 'Ελαφρά Χαλαζόπτωση',
                8000 => 'Καταιγίδα',
            ],
        ];

        $locale = app()->getLocale();
        $map    = $labels[$locale] ?? $labels['en'];
        $label  = $map[$code] ?? ($labels['en'][$code] ?? 'Unknown');
        $emoji  = $emojis[$code] ?? '🌡️';

        return [$label, $emoji];
    }

    /**
     * Wind direction degrees → compass label (locale-aware)
     */
    public static function windDir(float $deg): string
    {
        $locale = app()->getLocale();
        $dirs = $locale === 'el'
            ? ['Β', 'ΒΑ', 'Α', 'ΝΑ', 'Ν', 'ΝΔ', 'Δ', 'ΒΔ']
            : ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        return $dirs[(int) round($deg / 45) % 8];
    }

    /**
     * UV index → risk label + Tailwind color classes (locale-aware)
     */
    public static function uvRisk(float $uv): array
    {
        return match(true) {
            $uv < 3  => [__('water.uv_low'),       'text-emerald-600', 'bg-emerald-50 border-emerald-200'],
            $uv < 6  => [__('water.uv_moderate'),   'text-yellow-600',  'bg-yellow-50 border-yellow-200'],
            $uv < 8  => [__('water.uv_high'),       'text-orange-600',  'bg-orange-50 border-orange-200'],
            $uv < 11 => [__('water.uv_very_high'),  'text-red-600',     'bg-red-50 border-red-200'],
            default  => [__('water.uv_extreme'),     'text-purple-700',  'bg-purple-50 border-purple-200'],
        };
    }
}
