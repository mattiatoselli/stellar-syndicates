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
        Schema::create('users_ships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ship_id')->constrained('ships')->onDelete('cascade');
            $table->foreignId('star_location_id')->constrained('stars')->onDelete('cascade');
            $table->foreignId('planet_location_id')->constrained('planets')->onDelete('cascade');
            $table->string('status');
            $table->dateTime('end_of_operation_time')->nullable()->comment('in case of travels, fights, mining, etc. this is the datetime of when the ship will be available again');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_ships');
    }
};
