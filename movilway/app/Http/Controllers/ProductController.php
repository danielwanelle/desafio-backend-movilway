<?php

namespace App\Http\Controllers;

use App\Modules\ProductsModule;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        try {
            $products = ProductsModule::getProducts();

            return $this->successResponse(data: $products);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
