<?php

namespace Tests\Feature;

use App\Models\Pdv;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SaleTest extends TestCase
{
    /**
     * Create sale test.
     *
     * @test
     * @testdox Create sale test.
     * @covers \App\Http\Controllers\SaleController::store
     *
     * @return void
     */
    public function shouldCreateSale() : void
    {
        $pdv = Pdv::factory()->active()->create(['sales_limit' => 10000]);
        $sale = Sale::factory()->make();
        $products = array();

        foreach ($sale->products as $product) {
            $products[] = $product->id;
        }

        Http::fake(
            [
                'https://api.redeconekta.com.br/*' => Http::response($sale->products)
            ]
        );

        $response = $this->post(
            'api/sale',
            [
                'pdv_id' => $pdv->id,
                'products_ids' => $products,
            ]
        );

        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'pdv_id',
                    'products',
                    'value',
                    'created_at',
                    'updated_at',
                    'id'
                ]
            ]
        );
    }

    /**
     * Cancel a sale.
     *
     * @test
     * @testdox Cancel a sale.
     * @covers \App\Http\Controllers\SaleController::cancel
     *
     * @return void
     */
    public function shouldCancelSale() : void
    {
        $pdv = Pdv::factory()->active()->create(['sales_limit' => 10000]);
        $sale = Sale::factory()->pending()->create(['pdv_id' => $pdv->id]);

        $response = $this->delete(
            "api/sale/$sale->id",
            [
                'pdv_id' => $sale->pdv_id,
                'reason' => 'Test reason',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data'
            ]
        );
    }
}
