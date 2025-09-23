<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
            'plate_number' => 'required|string|max:10|unique:vehicles,plate_number',
            'brand' => 'required|string|max:50',
            'model' => 'required|numeric|min:1900|max:'.(date('Y')+1),
            'capacity' => 'required|numeric|min:100|max:10000',
            'status' => ['required', 'in:activo,mantenimiento,inactivo,servicio'],
        ];
    }
}
