<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserGoalRequest;
use App\Http\Resources\UserGoalResource;
use App\Models\UserGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGoalController extends Controller
{
    /**
     * Display the user's goals.
     */
    public function index()
    {
        $userGoals = UserGoal::where('user_id', Auth::id())->get();
        return UserGoalResource::collection($userGoals);
    }

    /**
     * Store or update the user's goals.
     */
    public function store()
    {
        $user = Auth::user(); // Get the authenticated user
    
        $userGoal = UserGoal::updateOrCreate(
            ['user_id' => $user->id],
            [
                'target_calories' => $this->calculateTargetCalories($user->weight, $user->height, $user->age, $user->gender, $user->goal),
                'target_water' => $this->calculateTargetWater($user->weight),
                'target_sleep' => $this->calculateTargetSleep($user->age),
            ]
        );
    
        return new UserGoalResource($userGoal);
    }
    

    /**
     * Update the user's goals manually.
     */
    public function update(Request $request, $id)
    {
        $userGoal = UserGoal::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$userGoal) {
            return response()->json(['message' => 'User goal not found'], 404);
        }

        $userGoal->update($request->only(['target_calories', 'target_water', 'target_sleep']));

        return new UserGoalResource($userGoal);
    }

    /**
     * Show a specific user goal.
     */
    public function show($id)
    {
        $userGoal = UserGoal::where('user_id', $id)->first();

        if (!$userGoal) {
            return response()->json(['message' => 'User goal not found'], 404);
        }

        return new UserGoalResource($userGoal);
    }

    /**
     * Calculate target calories based on user data.
     */
    private function calculateTargetCalories($weight, $height, $age, $gender, $goal)
    {
        // Basal Metabolic Rate (BMR)
        if ($gender === 'male') {
            $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
        } else {
            $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
        }

        // Adjust based on goal
        return match ($goal) {
            'less_weight' => max(1200, $bmr * 1.2 - 500),  // Minimum calories safeguard
            'get_weight' => $bmr * 1.2 + 500,  // Caloric surplus
            default => $bmr * 1.2,  // Maintenance
        };
    }

    /**
     * Calculate target water intake.
     */
    private function calculateTargetWater($weight)
    {
        return round(($weight * 0.033), 2); // Liters per day
    }

    /**
     * Calculate target sleep based on age.
     */
    private function calculateTargetSleep($age)
    {
        return ($age < 18) ? 9 : 7.5; // Fix: Returns 7.5 for adults
    }
}
