<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePdvsTable
 *
 * @package Database\Migrations
 */
return new class extends Migration
{
    /**
     * Table name.
     *
     * @var string
     */
    private const TABLE = 'pdvs';

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
                $table->string('fantasy_name');
                $table->string('cnpj');
                $table->string('owner_name');
                $table->string('owner_phone');
                $table->float('sales_limit');
                $table->boolean('active')->default(true);
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
