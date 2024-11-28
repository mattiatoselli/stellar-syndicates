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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('base_price');
            $table->text('description')->nullable();
            $table->integer('prospection_chance')->nullable();
            $table->foreignId('first_base_resource_id')->nullable()->constrained('resources')->onDelete('cascade');
            $table->foreignId('second_base_resource_id')->nullable()->constrained('resources')->onDelete('cascade');
            $table->integer('first_base_resource_quantity')->nullable();
            $table->integer('second_base_resource_quantity')->nullable();
            $table->float('rate_per_100k_population', 8, 2)->default(0)->after('name');
            $table->timestamps();
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
