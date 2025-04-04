<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Mail\ResetPasswordOtpMail;

class PasswordResetController extends Controller
{

public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ],
    [
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.exists' => 'Please enter a valid email address.'],
);

    $otp = rand(1000, 9999); // Generate 6-digit OTP

    // Save OTP in database
    PasswordResetOtp::updateOrCreate(
        ['email' => $request->email],
        ['otp' => $otp, 'created_at' => now()],
        ['verified'=>false]
    );

    // Send OTP email
    Mail::to($request->email)->send(new ResetPasswordOtpMail($otp));

    return response()->json(['message' => 'OTP sent to your email.'], 200);
}



public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|digits:4',
    ], [
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.exists' => 'This email is not Exsit',
        'otp.required' => 'The OTP field is required.',
        'otp.digits' => 'The OTP must be exactly 6 digits.',
    ]);


    // Check if OTP is valid
    $otpEntry = PasswordResetOtp::where('email', $request->email)
                                ->where('otp', $request->otp)
                                ->first();

    if (!$otpEntry) {
        return response()->json(['message' => 'Invalid OTP.'], 400);
    }
    $otpEntry->update(['verified' => true]);

    return response()->json(['message' => 'OTP verified. Proceed to reset password.'], 200);
}
public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6',
    ], [
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.exists' => 'Please enter a valid email address.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 6 characters long.',
    ]);

    // Check if OTP was verified
    $verified = PasswordResetOtp::where('email', $request->email)
                                ->where('verified', true)
                                ->exists();

    if (!$verified) {
        return response()->json(['message' => 'OTP verification required before resetting password.'], 403);
    }

    // Find user
    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    // Update password
    $user->password = Hash::make($request->password);
    $user->save();

    // Remove OTP record after reset
    PasswordResetOtp::where('email', $request->email)->delete();
    return response()->json([
        "data"=>new UserResource($user),
        "token" => $user->createToken($request->email)->plainTextToken
    ],200);
    // return response()->json(['message' => 'Password reset successful.'], 200);
}

}
