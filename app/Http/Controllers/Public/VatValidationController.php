<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ViesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VatValidationController extends Controller
{
    public function validate(Request $request, ViesService $vies): JsonResponse
    {
        $request->validate([
            'country'    => ['required', 'string', 'size:2'],
            'vat_number' => ['required', 'string', 'max:20'],
        ]);

        $result = $vies->validate(
            $request->input('country'),
            $request->input('vat_number')
        );

        return response()->json($result);
    }
}
