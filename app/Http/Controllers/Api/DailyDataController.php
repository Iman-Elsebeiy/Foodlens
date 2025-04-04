<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyData;
use App\Http\Requests\Api\DailyDataRequest;
use App\Http\Resources\Api\DailyDataResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;



class DailyDataController extends Controller
{
//     public function index()
//     {
//         return DailyDataResource::collection(DailyData::with('user')->get());

//         // $data = DailyData::where('user_id', Auth::id())->get();


//     }

    public function store(DailyDataRequest $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $data = DailyData::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->first();

        if ($data) {
            // Add to existing values
            $data->calories_consumed += $request->input('calories_consumed', 0);
            $data->water += $request->input('water', 0);
            $data->sleep += $request->input('sleep', 0);
            $data->weight = $request->input('weight', $data->weight); // Optional: replace or average
            $data->save();
        } else {
            // Create new record
            $data = DailyData::create([
                'user_id' => $userId,
                'calories_consumed' => $request->input('calories_consumed', 0),
                'water' => $request->input('water', 0),
                'sleep' => $request->input('sleep', 0),
                'weight' => $request->input('weight'), // First time weight is entered
            ]);
        }

        return new DailyDataResource($data);
    }

    public function show()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $dailyData = DailyData::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->first();

        if (!$dailyData) {
            return response()->json([
                'message' => 'Daily data not found.'
            ], 404);
        }

        return new DailyDataResource($dailyData);
    }



    //     $dailyData = DailyData::find($id);
    //     // Check if the authenticated user is the owner of the daily data
    //     $userId = Auth::id();
    //     if(!$userId ==$dailyData->user_id){
    //         return response()->json([
    //             'message' => 'Unauthorized access.'
    //         ], 403);
    //     }

    //     if (!$dailyData) {
    //         return response()->json([
    //             'message' => 'Daily data not found.'
    //         ], 404);
    //     }

    //     return new DailyDataResource($dailyData);
    // }


    public function update(DailyDataRequest $request, DailyData $dailyData)
    {
        $dailyData->update($request->validated());
        return new DailyDataResource($dailyData);
    }

    public function destroy(DailyData $dailyData)
    {
        $dailyData->delete();
        return response()->json(null, 204);
    }
}
