<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'description' => 'required|string|max:255',
            'type' => 'required|in:accidente,retraso,mecanico,otro',
            'reported_at' => 'required|date_format:Y-m-d\TH:i',
            'resolved' =>'required|numeric|min:0|max:1'
        ];
    }
}
