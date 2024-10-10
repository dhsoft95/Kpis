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
            'service' => [
//                'id' => $charge->subcategory->id,
                'name' => $charge->subcategory->name,
            ],
            'charge' => [
//                'id' => $charge->id,
                'type' => $charge->charge_type,
                'details' => $this->getChargeDetails($charge),
                'total_value' => $this->calculateTotalCharge($charge),
            ],
            'currency' => [
//                'id' => $charge->currency->id,
                'code' => $charge->currency->code,
                'name' => $charge->currency->name,
            ],
        ];
    }

    private function getChargeDetails(TransactionCharge $charge)
    {
        $details = [];

        if (in_array($charge->charge_type, ['percentage', 'both'])) {
            $details['percentage'] = [
                'value' => $charge->percentage,
                'formatted' => number_format($charge->percentage, 2) . '%',
            ];
        }

        if (in_array($charge->charge_type, ['fixed', 'both'])) {
            $details['fixed'] = [
                'value' => $charge->fixed_amount,
                'formatted' => number_format($charge->fixed_amount, 2),
            ];
        }

        return $details;
    }

    private function calculateTotalCharge(TransactionCharge $charge)
    {
        switch ($charge->charge_type) {
            case 'percentage':
                return $charge->percentage;
            case 'fixed':
                return $charge->fixed_amount;
            case 'both':
                return $charge->percentage + $charge->fixed_amount;
            default:
                return 0;
        }
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

    private function successResponse($data, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $code);
    }

    private function errorResponse($message, $code)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
