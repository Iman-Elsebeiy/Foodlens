<?php

namespace App\Http\Controllers\Api;

use App\Models\DailyData;
use App\Models\ScanHistory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\FoodScanRequest;
use App\Http\Resources\AllFoodScans;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FoodScanResource;

class FoodScanController extends Controller
{





    public function allScans()
    {
       $scans= AllFoodScans::collection(ScanHistory::paginate(3)->where('user_id',Auth::user()->id));
        return response()->json([
            'success' => true,
            'data' =>$scans
        ], 200);


    }

    public function scan(FoodScanRequest $request)
    {

        try {
            // store the image
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('food-scans', $imageName, 'public');

            // encode image as base64 to send it
            $imageData = base64_encode(file_get_contents($image->getRealPath()));


            $apiKey = 'mSmxuIL3QGQ8uVLQNhkw';
            $url = "https://detect.roboflow.com/graduation-project-l58sq/2?api_key={$apiKey}";

            //  request to AI model
            $response = Http::withBody($imageData, 'application/x-www-form-urlencoded')
                ->post($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get AI classification',
                    'error_details' => $response->body(),
                    'status_code' => $response->status()
                ], 500);
            }

            $aiResponse = $response->json();

            // process the AI response
            $predictions = $aiResponse['predictions'] ?? [];
            $foodItems = array_map(fn($prediction) => [
                'name' => $prediction['class'],
                'confidence' => $prediction['confidence']
            ], $predictions);

            $foodNames = array_column($foodItems, 'name');
            if (empty($foodNames)) {
                return response()->json([
                    "success" => false,
                    "message" => "No valid food names found."
                ], 400);
            }
            $food = DB::table('foods')
                ->where(function ($query) use ($foodNames) {
                    foreach ($foodNames as $name) {
                        $keywords = explode('-', $name);
                        foreach ($keywords as $word) {
                            $query->orWhere('food_name', 'LIKE', "%$word%");
                        }
                    }
                })
                ->first();
            if (!$food) { // Food not found
                return response()->json([
                    "success" => false,
                    "message" => "Food not found in database."
                ], 404);
            }

            $weight = floatval(preg_replace('/[^0-9.]/', '', $food->weight_with_unit));

            $calCalorie = round(($food->calories_per_100g * ($weight * $request->input('total_food_items'))) / 100, 2);
            $dailyData = new DailyData();
            $dailyData->user_id = Auth::user()->id;
            $dailyData->calories_consumed = $calCalorie;
            $dailyData->save();
            ScanHistory::create(
                [
                    "total_food_items" => $request->input('total_food_items'),
                    "calories_consumed" => $calCalorie,
                    "image" => Storage::url($imagePath),
                    "user_id" => Auth::user()->id,
                    "food_id" => $food->id,
                    "daily_data_id" => $dailyData->id
                ]

            );
            $data = [

                "total_calories" => $calCalorie,
                'image_path' => Storage::url($imagePath),
                'food_items' => $foodNames[0] ?? 'Unknown',
                'total_food_items' => $request->input('total_food_items')
            ];

            return new FoodScanResource($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteScan($id)
    {
     ScanHistory::findOrFail($id)->delete();


        return response()->json([
            'success' => true,
            'message' => 'Scan history deleted successfully.'
        ], 200);
    }
}
