<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSalesTable
 *
 * @package Database\Migrations
 */
return new class extends Migration
{
    private const TABLE = 'sales';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            self::TABLE,
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('pdv_id')->constrained('pdvs');
                $table->json('products');
                $table->float('value');
                $table->string('cancel_reason')->nullable();
                $table->integer('status')->default(0);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
};
