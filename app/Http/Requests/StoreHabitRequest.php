<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHabitRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The habit name is required.',
            'name.max' => 'The habit name must not exceed 255 characters.',
            'frequency.required' => 'Please select a frequency.',
            'frequency.in' => 'The selected frequency is invalid.',
            'color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',
        ];
    }
}
