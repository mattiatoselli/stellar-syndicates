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
        Schema::create('planets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('star_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->float('distance_from_star');
            $table->string('type');
            $table->integer('population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planets');
    }
};
