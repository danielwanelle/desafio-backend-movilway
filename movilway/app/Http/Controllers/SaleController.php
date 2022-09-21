<?php

namespace App\Http\Controllers;

use App\Models\Pdv;
use App\Models\Sale;
use App\Modules\ProductsModule;
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
    public function store(Request $request)
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

            $pdv = Pdv::find($request->pdv_id);
            // Verificar se está ativo

            if (!$pdv) {
                throw new \Exception('Pdv not found');
            }

            $products = ProductsModule::getSelectedProducts($request->products_ids);
            $total = ProductsModule::calculateTotal($products);

            $saleData = [
                'pdv_id' => $pdv->id,
                'products' => $products,
                'value' => $total,
            ];

            if (!$pdv->canSale()) {
                Sale::create($saleData + ['status' => Sale::STATUS_REJECTED]);

                throw new \Exception('Limit exceeded');
            }

            $sale = Sale::create($saleData);

            return $this->successResponse(status: 201, data: $sale);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
