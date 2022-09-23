<?php

namespace Tests\Feature;

use App\Models\Pdv;
use Tests\TestCase;

class PdvTest extends TestCase
{
    /**
     * Show a PDV.
     *
     * @test
     * @testdox Show a PDV.
     * @covers \App\Http\Controllers\PdvController::show
     *
     * @return void
     */
    public function shouldReturnAPdv() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $this->get("/api/pdv/$pdv->id");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'id',
                    'fantasy_name',
                    'cnpj',
                    'owner_name',
                    'owner_phone',
                    'sales_limit',
                    'active',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
    }

    /**
     * List all Pdvs test.
     *
     * @test
     * @testdox List all Pdvs
     * @covers \App\Http\Controllers\PdvController::index
     *
     * @return void
     */
    public function shouldListAllPdvs() : void
    {
        $response = $this->get('api/pdv');

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data',
            ]
        );
    }

    /**
     * Get a pdv debt
     *
     * @test
     * @testdox Get a pdv debt
     * @covers \App\Http\Controllers\PdvController::debt
     *
     * @return void
     */
    public function shouldReturnPdvDebt() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $this->get("/api/pdv/$pdv->id/debt");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data'
            ]
        );
        $this->assertEquals(0, $response->json('data')[0]);
    }

    /**
     * Create a pdv
     *
     * @test
     * @testdox Create a pdv
     * @covers \App\Http\Controllers\PdvController::store
     *
     * @return void
     */
    public function shouldCreateAPdv() : void
    {
        $pdv = Pdv::factory()->active()->make();

        $response = $this->post('api/pdv', $pdv->toArray());

        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'id',
                    'fantasy_name',
                    'cnpj',
                    'owner_name',
                    'owner_phone',
                    'sales_limit',
                    'active',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
    }

    /**
     * Update a pdv
     *
     * @test
     * @testdox Update a pdv
     * @covers \App\Http\Controllers\PdvController::update
     *
     * @return void
     */
    public function shouldUpdateAPdv() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $this->put(
            "api/pdv/$pdv->id",
            ['owner_phone' => fake()->numerify('(##) #####-####')]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'id',
                    'fantasy_name',
                    'cnpj',
                    'owner_name',
                    'owner_phone',
                    'sales_limit',
                    'active',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
        $this->assertFalse($response->json('data')['active']);
    }

    /**
     * Delete a pdv
     *
     * @test
     * @testdox Delete a pdv
     * @covers \App\Http\Controllers\PdvController::destroy
     *
     * @return void
     */
    public function shouldDeleteAPdv() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $this->delete("api/pdv/$pdv->id");

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data'
            ]
        );
        $this->assertFalse($response->json('data'));
    }

    /**
     * Set a pdv limit
     *
     * @test
     * @testdox Set a pdv limit
     * @covers \App\Http\Controllers\PdvController::setLimit
     *
     * @return void
     */
    public function shouldSetPdvLimit() : void
    {
        $pdv = Pdv::factory()->active()->create();
        $newValue = fake()->randomFloat(2, 1000, 10000);

        $response = $this->put(
            "api/pdv/$pdv->id/limit",
            ['sales_limit' => $newValue]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'id',
                    'fantasy_name',
                    'cnpj',
                    'owner_name',
                    'owner_phone',
                    'sales_limit',
                    'active',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
        $this->assertEquals($newValue, $response->json('data')['sales_limit']);
    }

    /**
     * Quit pdv debts
     *
     * @test
     * @testdox Quit pdv debts
     * @covers \App\Http\Controllers\PdvController::quitDebt
     *
     * @return void
     */
    public function shouldQuitPdvDebts() : void
    {
        $pdv = Pdv::factory()->active()->create();

        $response = $this->put(
            "api/pdv/$pdv->id/debt/quit",
            ['payment_value' => $pdv->getTotalPendingSales()]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'message',
                'status',
                'data' => [
                    'id',
                    'fantasy_name',
                    'cnpj',
                    'owner_name',
                    'owner_phone',
                    'sales_limit',
                    'active',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
        $this->assertEquals(
            $pdv->getFreeLimit(),
            $response->json('data')['sales_limit']
        );
    }
}
