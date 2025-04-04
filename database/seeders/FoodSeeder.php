<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $jsonFile = database_path('seeders/data/food.json');

        if (!file_exists($jsonFile)) {
            $this->command->error("JSON file not found: {$jsonFile}");
            return;
        }

        $jsonData = file_get_contents($jsonFile);
        $foods = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON format: " . json_last_error_msg());
            return;
        }

        $insertedCount = 0;
        $skippedCount = 0;
        $batch = [];
        $batchSize = 100;

        $this->command->info("Starting food data import from JSON...");

        foreach ($foods as $index => $foodItem) {
            try {

                // Validate
                if (empty($foodItem['FoodItems']) ||
                    empty($foodItem['Quantity']) ||
                    empty($foodItem['Calories'])) {
                    $skippedCount++;
                    continue;
                }

                $foodName = trim($foodItem['FoodItems']);
                $quantityDescription = trim($foodItem['Quantity']);
                $calories = (int)preg_replace('/[^0-9]/', '', $foodItem['Calories']);

                // parse quantity and weight information
                $quantity = 1;
                $weightWithUnit = null;
                $caloriesPer100g = null;

                // Extract quantity (e.g., "1" in "1 artichoke")
                if (preg_match('/^(\d+)\s/', $quantityDescription, $qtyMatches)) {
                    $quantity = (int)$qtyMatches[1];
                }

                // Extract weight with unit (e.g., "128 g") from parentheses
                if (preg_match('/\(([^)]+)\)/', $quantityDescription, $matches)) {
                    $weightWithUnit = trim($matches[1]);

                    // Calculate calories per 100g if we have weight information
                    if (preg_match('/(\d+(?:\.\d+)?)\s*([a-zA-Z]+)/', $weightWithUnit, $weightMatches)) {
                        $weightValue = (float)$weightMatches[1];
                        if ($weightValue > 0) {
                            $caloriesPer100g = round(($calories / $weightValue) * 100, 2);
                        }
                    }
                }

                $batch[] = [
                    'food_name' => $foodName,
                    'quantity_description' => $quantityDescription,
                    'quantity' => $quantity,
                    'weight_with_unit' => $weightWithUnit,
                    'total_calories' => $calories,
                    'calories_per_100g' => $caloriesPer100g,
                    'category' => $this->determineCategory($foodName),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Insert in batches
                if (count($batch) >= $batchSize) {
                    DB::table('foods')->insert($batch);
                    $insertedCount += count($batch);
                    $batch = [];
                    $this->command->info("Processed {$insertedCount} records...");
                }

            } catch (\Exception $e) {
                Log::error("Error processing item #{$index}: " . $e->getMessage());
                $skippedCount++;
                continue;
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            DB::table('foods')->insert($batch);
            $insertedCount += count($batch);
        }

        $this->command->info("\nImport completed!");
        $this->command->info("Total items processed: " . count($foods));
        $this->command->info("Successfully inserted: {$insertedCount}");
        $this->command->warn("Skipped: {$skippedCount}");

        if ($skippedCount > 0) {
            $this->command->warn("Some records were skipped. Check the log file for details.");
        }
    }

    private function determineCategory(string $foodName): string
    {
        $foodName = strtolower($foodName);

        $categories = [
            'Vegetable' => ['artichoke', 'arugula', 'asparagus', 'broccoli', 'carrot', 'spinach'],
            'Fruit' => ['apple', 'banana', 'orange', 'strawberry', 'grape', 'mango'],
            'Grain' => ['rice', 'bread', 'pasta', 'wheat', 'oat', 'corn'],
            'Protein' => ['chicken', 'beef', 'fish', 'egg', 'pork', 'tofu'],
            'Dairy' => ['milk', 'cheese', 'yogurt', 'cream'],
            'Prepared Food' => ['pizza', 'burger', 'sandwich', 'soup'],
            'Snack' => ['chips', 'cookie', 'ice cream', 'crackers']
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($foodName, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'Other';
    }

}