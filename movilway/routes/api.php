<?php

use App\Http\Controllers\PdvController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(PdvController::class)->group(
    function () {
        Route::prefix('pdvs')->group(
            function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::put('/{pdv}/set_limit', 'setLimit');
                Route::put('/{pdv}/pay_limit', 'payLimit');
                Route::get('/{pdv}', 'show');
                Route::patch('/{pdv}', 'update');
                Route::delete('/{pdv}', 'destroy');
            }
        );
    }
);

Route::controller(SaleController::class)->group(
    function () {
        Route::prefix('sales')->group(
            function () {
                Route::post('/', 'store');
                Route::delete('/{sale}', 'cancel');
            }
        );
    }
);

Route::controller(ProductController::class)->group(
    function () {
        Route::prefix('products')->group(
            function () {
                Route::get('/', 'index');
            }
        );
    }
);
