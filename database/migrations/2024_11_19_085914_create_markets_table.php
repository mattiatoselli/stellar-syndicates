<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_planets_table.php

    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->foreignId('planet_id')->constrained('planets')->onDelete('cascade');
            $table->unsignedBigInteger('stock')->default(0)->comment('this is the stock of this resource');
            $table->unsignedBigInteger('base_production')->default(0)->comment('to simulate the base production of the planet, it will decrease over time to simulate the depletion of resources and allow the market to be influenced by players');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
