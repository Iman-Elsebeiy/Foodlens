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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('food_name')->index(); // Add index for faster searching
            $table->string('quantity_description')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('weight_with_unit')->nullable();
            $table->integer('total_calories')->nullable();
            $table->float('calories_per_100g')->nullable();
            $table->enum('category', [
                'Vegetable',
                'Fruit',
                'Grain',
                'Protein',
                'Dairy',
                'Prepared Food',
                'Snack',
                'Soup',
                'Pizza',
                'Sandwich',
                'Other'
            ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
