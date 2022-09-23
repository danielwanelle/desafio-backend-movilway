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
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /**
     * Should return sales instance for the Pdv.
     *
     * @test
     * @testdox Should return sales instance for the Pdv.
     * @covers \App\Models\Pdv::sales
     *
     * @return void
     */
    // public function shouldGetSales()
    // {
    //     $pdv = Pdv::factory()->make();
    //     $sales = Sale::factory()->count(5)->make(['pdv_id' => $pdv->id]);

    //     // $this->assertInstanceOf(HasMany::class, $pdv->sales());
    //     $this->assertInstanceOf(Sale::class, $pdv->sales()->first());
    //     $this->assertCount(5, $pdv->sales);
    //     $this->assertEquals($sales->first()->id, $pdv->sales()->first()->id);
    // }
}
