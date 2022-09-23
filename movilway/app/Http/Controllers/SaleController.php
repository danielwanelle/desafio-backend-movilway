<?php

namespace App\Http\Controllers;

use App\Models\Pdv;
use App\Models\Sale;
use App\Modules\ProductsModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Sale controller
 *
 * @package App\Http\Controllers
 */
class SaleController extends Controller
{
    public function store(Request $request) : JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'pdv_id' => ['required', 'integer'],
                    'products_ids' => ['required', 'array'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pdv = Pdv::active()->findOrFail($request->pdv_id);
            $products = ProductsModule::getSelectedProducts($request->products_ids);
            $total = ProductsModule::calculateTotal($products);

            $saleData = [
                'pdv_id' => $pdv->id,
                'products' => $products,
                'value' => $total,
            ];

            if (!$pdv->canSale($total)) {
                Sale::create($saleData + ['status' => Sale::STATUS_REJECTED]);

                throw new \Exception('Limit exceeded');
            }

            $sale = Sale::create($saleData);

            return $this->successResponse(status: 201, data: $sale);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }

    /**
     * Cancel a sale.
     *
     * @param Request $request Request
     * @param Sale    $sale    Sale
     *
     * @return JsonResponse
     */
    public function cancel(Request $request, Sale $sale) : JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'pdv_id' => ['required', 'integer'],
                    'reason' => ['required', 'string'],
                ]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pdv = Pdv::active()->findOrFail($request->pdv_id);

            if (!$pdv->hasSale($sale)) {
                throw new \Exception('Sale not found');
            }

            $cancelled = $sale->cancel($request->reason);

            if (!$cancelled) {
                throw new \Exception('Could not cancel sale');
            }

            return $this->successResponse(data: $sale);
        } catch (\Exception $e) {
            return $this->failureResponse($e->getMessage());
        }
    }
}
