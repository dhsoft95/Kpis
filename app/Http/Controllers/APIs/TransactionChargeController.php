<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionChargeController extends Controller
{
    /**
     * Retrieve charges based on service name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChargesByService(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
        ]);

        $serviceName = $request->input('service_name');

        Log::info("Searching for service: " . $serviceName);

        $subcategory = Subcategory::where('name', $serviceName)->first();
        if (!$subcategory) {
            Log::warning("Subcategory not found: " . $serviceName);
            return $this->errorResponse('Service not found.', 404);
        }

        Log::info("Subcategory found with ID: " . $subcategory->id);

        $charge = TransactionCharge::where('is_active', true)
            ->where('service_id', $subcategory->id)
            ->with(['subcategory', 'currency'])
            ->first();

        Log::info("Charge query result: " . ($charge ? "Found" : "Not found"));

        if (!$charge) {
            return $this->errorResponse('No charges found for the specified service.', 404);
        }

        return $this->successResponse($this->formatChargeData($charge));
    }

    private function formatChargeData(TransactionCharge $charge)
    {
        return [
            $charge->subcategory->name => [
                'charge_type' => $charge->charge_type,
                'charging_rate' => $charge->charge_type === 'fixed' ? '0' : $charge->percentage,
                'charging_fixed_amount' => $charge->charge_type === 'percentage' ? '0' : $charge->fixed_amount,
            ],
        ];
    }

    private function successResponse($data, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $code);
    }


    public function GetServices(): \Illuminate\Http\JsonResponse
    {
        $subcategories = Subcategory::select('id', 'name')->get();
        return $this->formatResponse($subcategories, 'SML-SERVICES');
    }

    private function formatResponse($data, $key)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                $key => $data->map(function ($item) {
                    return [
                        'name' => $item->name
                    ];
                })
            ],
        ]);
    }



    private function errorResponse($message, $code)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
