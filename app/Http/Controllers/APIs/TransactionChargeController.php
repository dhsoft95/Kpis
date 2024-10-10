<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;

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

        $charge = TransactionCharge::where('service_name', $serviceName)
            ->where('is_active', true)
            ->with('currency')
            ->first();

        if (!$charge) {
            return response()->json([
                'message' => 'No charges found for the specified service.',
            ], 404);
        }

        $response = [
            'service_name' => $charge->service_name,
            'charging_type' => $charge->charge_type,
            'charger' => $this->formatCharger($charge),
        ];

        return response()->json($response);
    }

    /**
     * Format the charger object based on the charge type.
     *
     * @param  TransactionCharge  $charge
     * @return array
     */
    private function formatCharger(TransactionCharge $charge)
    {
        switch ($charge->charge_type) {
            case 'percentage':
                return ['percent' => $charge->percentage];
            case 'fixed':
                return ['flat' => $charge->fixed_amount];
            case 'both':
                return [
                    'percent' => $charge->percentage,
                    'flat' => $charge->fixed_amount,
                    'sum' => $charge->percentage + $charge->fixed_amount
                ];
            default:
                return [];
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

}
