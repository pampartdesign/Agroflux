<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ViesService
{
    /**
     * Validate an EU VAT number via the VIES REST API.
     * Returns ['valid' => bool, 'name' => string, 'address' => string]
     */
    public function validate(string $countryCode, string $vatNumber): array
    {
        $vatNumber = preg_replace('/\s+/', '', strtoupper($vatNumber));
        $countryCode = strtoupper(trim($countryCode));

        $url = "https://ec.europa.eu/taxation_customs/vies/rest-api/ms/{$countryCode}/vat/{$vatNumber}";

        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->ok()) {
                return ['valid' => false, 'name' => '', 'address' => ''];
            }

            $data = $response->json();

            return [
                'valid'   => (bool) ($data['valid'] ?? false),
                'name'    => $data['name'] ?? '',
                'address' => $data['address'] ?? '',
            ];
        } catch (\Throwable) {
            return ['valid' => false, 'name' => '', 'address' => ''];
        }
    }
}
