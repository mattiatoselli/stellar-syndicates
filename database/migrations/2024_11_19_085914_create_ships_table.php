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
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('the name of the spaceship');
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->integer('hull')->comment('health of the ship');
            $table->integer('damage')->comment('y coordinate');
            $table->integer('survey')->comment('bonus in capacity of discovering new resources on planets');
            $table->integer('mining_speed')->comment('bonus in mining resources');
            $table->integer('cargo')->comment('cargo max capacity');
            $table->integer('cargo_speed')->comment('cargo_speed in units every ten seconds');
            $table->integer('speed')->comment('speed of the ship. Affects the speed of the movement beetween planets and stars. expressed in distance per hour');
            $table->integer('fuel_capacity')->default(100)->comment('maximum fuel capacity');
            $table->integer('fuel_distance_ratio')->comment('fuel units burned per every distance unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
