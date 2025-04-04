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
            Schema::create('scan_histories', function (Blueprint $table) {
                $table->id();
                $table->integer('total_food_items')->nullable();
                $table->integer('calories_consumed')->nullable();
                $table->string('image');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('food_id');
                $table->unsignedBigInteger('daily_data_id');
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                // $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
                // $table->foreign('daily_data_id')->references('id')->on('daily_data')->onDelete('cascade');
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_histroy');
    }
};