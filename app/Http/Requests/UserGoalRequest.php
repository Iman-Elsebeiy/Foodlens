<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserGoalRequest extends FormRequest
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
            'target_calories' => 'nullable|integer|min:1000|max:5000',
            'target_water' => 'nullable|numeric|min:0.5|max:10',
            'target_sleep' => 'nullable|numeric|min:4|max:12',
            
        ];
    }
}
