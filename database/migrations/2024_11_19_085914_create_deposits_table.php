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
        Schema::create('deposits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('planet_id')->nullable()->constrained('planets')->onDelete('cascade');
            $table->foreignId('resource_id')->nullable()->constrained('resources')->onDelete('cascade');
            $table->string('status');
            $table->foreignId('discovered_by')->nullable()->constrained('users');
            $table->unsignedBigInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
