<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique()->comment('the name of the star');
            $table->string('spectrum');
            $table->string('color');
            $table->integer('temperature');
            $table->float('x')->comment('x coordinate');
            $table->float('y')->comment('y coordinate');
            $table->float('z')->comment('z coordinate');
            $table->unique(['x', 'y', 'z']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
