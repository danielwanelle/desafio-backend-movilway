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
        Route::prefix('pdv')->group(
            function () {
                Route::get('/', 'index');
                Route::post('/', 'store');

                Route::prefix('{pdv}')->group(
                    function () {
                        Route::get('/', 'show');
                        Route::patch('/', 'update');
                        Route::put('limit', 'setLimit');
                        Route::delete('/', 'destroy');
                        
                        Route::prefix('debt')->group(
                            function () {
                                Route::get('/', 'getDebt');
                                Route::put('quit', 'quitDebt');
                            }
                        );
                    }
                );
            }
        );
    }
);

Route::controller(SaleController::class)->group(
    function () {
        Route::prefix('sale')->group(
            function () {
                Route::post('/', 'store');
                Route::delete('/{sale}', 'cancel');
            }
        );
    }
);

Route::controller(ProductController::class)->group(
    function () {
        Route::prefix('product')->group(
            function () {
                Route::get('/', 'index');
            }
        );
    }
);
