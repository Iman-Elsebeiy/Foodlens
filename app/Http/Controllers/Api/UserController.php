<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller

{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $imagename = null;
        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imagename=$image->store('users', 'public');
        }
        $request_data = $request->except(['image']);
        $request_data['image'] = $imagename;
        $user = User::create($request_data);
        // // return response()->json($user)->setStatusCode(200);
        $user_data = [
            "id"=> $user->id,
           "email"=>$user->email,
           "name" => $user->name,
           "image"=>$user->image,
       ];
        return response()->json([
            "data"=>$user_data,
            "token" => $user->createToken($request->email)->plainTextToken
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ((int) $id !== Auth::id()) {
            return response()->json([
                "error"=>['message' => 'Bad Request Cannot Access Data About Other User']], 403);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "error"=>['message' => 'Canot Found']], 403);
        }

        return new UserResource($user);

    }



    public function update(UserUpdateRequest $request, User $user)
    {

        if ((int) $user['id'] !== Auth::id()) {
            return response()->json([
                "error"=>['message' => 'Bad Request Cannot Access Data About Other User']], 403);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $image = $request->file('image');
            $imagename = $image->store('users', 'public');
            $user->image = $imagename;
        }

        $user->fill($request->except(['image', 'password']));
        $user->save();

        return response()->json([
            "data" => new UserResource($user),
            "message" => "User updated successfully"
        ], 200);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
                'password' => ['The provided credentials are incorrect.'],
            ]);
        }
        $user_data = [
             "id"=> $user->id,
            "email"=>$user->email,
            "name" => $user->name,
            "image"=>$user->image,
        ];

        return response()->json([
            "data" => $user_data,
            "token" => $user->createToken($request->email)->plainTextToken
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ((int) $user['id'] !== Auth::id()) {
            return response()->json([
                "error"=>['message' => 'Bad Request Cannot Access Data About Other User']], 403);
        }

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->delete();
        return response()->json(
            ["success" => ['message' => 'Account Deleted Sucessfully Successfully']], 200);

    }
}
