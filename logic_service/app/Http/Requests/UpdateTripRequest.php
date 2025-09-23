<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
        'vehicle_id' => 'required|exists:vehicles,id',
        'driver_id'  => 'required|exists:drivers,id',
        'route_id'   => 'required|exists:routes,id',
        'end_time'   => 'nullable|date|after:start_time',
        'status'     => 'required|in:scheduled,in_progress,completed,cancelled',
        ];
    }
}
