<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,',
            'password' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'weight' => 'nullable|numeric|min:20|max:500',
            'target_weight' => 'nullable|numeric|min:20|max:500',
            'height' => 'nullable|numeric|min:50|max:300',
            'goal' => 'nullable|in:less_weight,get_weight,keep_weight'
        ];
    }
    

    public function messages()
    {
        return [
           'name.required'          => 'Please enter your name.',
            'email.required'         => 'Please enter your email address.',
            'email.email'            => 'Please enter a valid email address.',
            'email.unique'           => 'This email is already in use.',
            'password.required'      => 'Please enter a password.',
            'password.min'           => 'Your password must be at least 8 characters long.',
            'image.image'            => 'Please upload a valid image file.',
            'image.mimes'            => 'Only JPEG, PNG, or JPG images are allowed.',
            'image.max'              => 'The image size must not exceed 2 MB.',
            'weight.numeric'         => 'Weight must be a numeric value.',
            'weight.min'             => 'Weight must be at least 20.',
            'weight.max'             => 'Weight must not exceed 500.',
            'target_weight.numeric'  => 'Target weight must be a numeric value.',
            'target_weight.min'      => 'Target weight must be at least 20.',
            'target_weight.max'      => 'Target weight must not exceed 500.',
            'height.numeric'         => 'Height must be a numeric value.',
            'height.min'             => 'Height must be at least 50.',
            'height.max'             => 'Height must not exceed 300.',
            'gender.in'              => 'Gender must be either male or female.',
            'goal.in'               =>"Goal must be less_weight or get_weight or keep_weight"
         ];
    }
}