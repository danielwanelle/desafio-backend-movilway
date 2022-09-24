<?php

namespace Tests\Unit\Models;

use App\Models\Pdv;
use App\Models\Sale;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

// use PHPUnit\Framework\TestCase;

class PdvTest extends TestCase
{
    /**
     * Should return sales instance for the Pdv.
     *
     * @test
     * @testdox Should return sales instance for the Pdv.
     * @covers \App\Models\Pdv::sales
     *
     * @return void
     */
    public function shouldGetSales() : void
    {
        $pdv = Pdv::factory()->create();
        $sales = Sale::factory()->count(5)->create(['pdv_id' => $pdv->id]);

        $this->assertInstanceOf(HasMany::class, $pdv->sales());
        $this->assertInstanceOf(Sale::class, $pdv->sales()->first());
        $this->assertCount(5, $pdv->sales);
        $this->assertEquals($sales->first()->id, $pdv->sales()->first()->id);
    }

    /**
     * Update a Pdv limit
     *
     * @test
     * @testdox Update a Pdv limit
     * @covers \App\Models\Pdv::updateSalesLimit
     *
     * @return void
     */
    public function shouldUpdateSalesLimit() : void
    {
        $pdv = Pdv::factory()->create();
        $newLImit = 1000.00;

        $pdv = $pdv->updateSalesLimit($newLImit);

        $this->assertInstanceOf(Pdv::class, $pdv);
        $this->assertEquals($newLImit, $pdv->sales_limit);
    }

    /**
     * Not update a Pdv limit
     *
     * @test
     * @testdox Not update a Pdv limit
     * @covers \App\Models\Pdv::updateSalesLimit
     *
     * @return void
     */
    public function shouldntUpdateSalesLimit() : void
    {
        $pdv = Pdv::factory()->create();
        $oldLimit = $pdv->sales_limit;
        $newLImit = -1000.00;

        $response = $pdv->updateSalesLimit($newLImit);
        $pdv->fresh();

        $this->assertNull($response);
        $this->assertEquals($oldLimit, $pdv->sales_limit);
    }

    /**
     * Return the total of pending sales for a Pdv
     *
     * @test
     * @testdox Return the total of pending sales for a Pdv 
     * @covers \App\Models\Pdv::getTotalPendingSales
     *
     * @return void
     */
    public function shouldGetTotalPendingSales() : void
    {
        $pdv = Pdv::factory()->active()->create();
        $sales = Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);

        $salesValue = $sales->sum('value');

        $total = $pdv->getTotalPendingSales();

        $this->assertEquals($salesValue, $total);
    }


    /**
     * Get the free limit for a Pdv
     *
     * @test
     * @testdox Get the free limit for a Pdv 
     * @covers \App\Models\Pdv::getFreeLimit
     *
     * @return void
     */
    public function shouldgetFreeLimit() : void
    {
        $limit = 50000.00;
        $pdv = Pdv::factory()->active()->create(['sales_limit' => $limit]);
        $sales = Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);

        $salesValue = $sales->sum('value');
        $freeLimit = $pdv->sales_limit - $salesValue;

        $total = $pdv->getFreeLimit();

        $this->assertEquals($freeLimit, $total);
    }

    /**
     * Verify if Pdv can sale
     *
     * @test
     * @testdox Verify if Pdv can sale 
     * @covers \App\Models\Pdv::canSale
     *
     * @return void
     */
    public function shouldVerifyPdvCanSale() : void
    {
        $limit = 50000.00;
        $pdv = Pdv::factory()->active()->create(['sales_limit' => $limit]);
        $sales = Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);
        $salesValue = $sales->sum('value');
        $freeLimit = $pdv->sales_limit - $salesValue;

        $canSale = $pdv->canSale($freeLimit);

        $this->assertTrue($canSale);
    }

    /**
     * Verify if Pdv cant sale
     *
     * @test
     * @testdox Verify if Pdv cant sale 
     * @covers \App\Models\Pdv::canSale
     *
     * @return void
     */
    public function shouldVerifyPdvCantSale() : void
    {
        $limit = 50000.00;
        $pdv = Pdv::factory()->active()->create(['sales_limit' => $limit]);
        Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);
        $canSale = $pdv->canSale($limit);

        $this->assertFalse($canSale);
    }

    /**
     * Quit Pdv Debts
     *
     * @test
     * @testdox Quit Pdv Debts 
     * @covers \App\Models\Pdv::paySalesLimit
     *
     * @return void
     */
    public function shouldPaySalesLimit() : void
    {
        $limit = 50000.00;
        $pdv = Pdv::factory()->active()->create(['sales_limit' => $limit]);
        $sales = Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);

        $salesValue = $sales->sum('value');

        $response = $pdv->paySalesLimit($salesValue);

        $this->assertTrue($response);
        $this->assertEquals(0, $pdv->getTotalPendingSales());
    }

    /**
     * Dont quit Pdv Debts
     *
     * @test
     * @testdox Dont quit Pdv Debts 
     * @covers \App\Models\Pdv::paySalesLimit
     *
     * @return void
     */
    public function shouldntPaySalesLimit() : void
    {
        $limit = 50000.00;
        $pdv = Pdv::factory()->active()->create(['sales_limit' => $limit]);
        
        $this->assertFalse($pdv->paySalesLimit($limit));

        $sales = Sale::factory()->pending()->count(5)->create(['pdv_id' => $pdv->id]);

        $this->assertFalse($pdv->paySalesLimit($limit));
    }

    /**
     * Deactivate a Pdv
     *
     * @test
     * @testdox Deactivate a Pdv 
     * @covers \App\Models\Pdv::deactivate
     *
     * @return void
     */
    public function shouldDeactivatePdv() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $pdv->deactivate();

        $this->assertTrue($response);
        $this->assertFalse($pdv->active);
    }

    /**
     * Verify is a Pdv active
     *
     * @test
     * @testdox Verify is a Pdv active 
     * @covers \App\Models\Pdv::isActive
     *
     * @return void
     */
    public function shouldVerifyIsPdvActive() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $this->assertTrue($pdv->isActive());
    }

    /**
     * Verify if Pdv have a sale
     *
     * @test
     * @testdox Verify if Pdv have a sale 
     * @covers \App\Models\Pdv::hasSale
     *
     * @return void
     */
    public function shouldVerifyIsPdvHasSale() : void
    {
        $pdv = Pdv::factory()->active()->create();
        $sale = Sale::factory()->pending()->create(['pdv_id' => $pdv->id]);

        $this->assertTrue($pdv->hasSale($sale));
    }
}
