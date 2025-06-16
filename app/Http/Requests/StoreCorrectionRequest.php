<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Izinkan semua pengguna yang terautentikasi untuk membuat request ini
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
            'date_to_correct' => 'required|date_format:Y-m-d|before_or_equal:today',
            'new_check_in' => 'nullable|date_format:H:i',
            'new_check_out' => 'nullable|date_format:H:i|after:new_check_in',
            'new_activity_title' => 'nullable|string|max:255',
            'new_activity_description' => 'nullable|string',
            'reason' => 'required|string|max:1000',
        ];
    }
}