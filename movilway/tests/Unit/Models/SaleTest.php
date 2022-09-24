<?php

namespace Tests\Unit\Models;

use App\Models\Pdv;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class SaleTest extends TestCase
{
    /**
     * Should return the Pdv who a sale belongs to.
     *
     * @test
     * @testdox Should return the Pdv who a sale belongs to.
     * @covers \App\Models\Pdv::pdv
     *
     * @return void
     */
    public function shouldGetSales() : void
    {
        $pdv = Pdv::factory()->active()->create();
        $sale = Sale::factory()->create(['pdv_id' => $pdv->id]);

        $this->assertInstanceOf(BelongsTo::class, $sale->pdv());
        $this->assertInstanceOf(Pdv::class, $sale->pdv()->first());
        $this->assertEquals($pdv->first()->id, $sale->pdv()->first()->id);
    }

    /**
     * Cancel a sale.
     *
     * @test
     * @testdox Cancel a sale.
     * @covers \App\Models\Pdv::cancel
     *
     * @return void
     */
    public function shouldCancelSale() : void
    {
        $pdv = Pdv::factory()->active()->create();
        $sale = Sale::factory()->pending()->create(['pdv_id' => $pdv->id]);

        $response = $sale->cancel('Test reason');

        $this->assertTrue($response);
        $this->assertEquals(Sale::STATUS_CANCELED, $sale->status);
        $this->assertEquals('Test reason', $sale->cancel_reason);
    }
}
