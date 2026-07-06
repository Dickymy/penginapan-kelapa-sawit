<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'exists:room_types,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('rooms')->ignore($this->route('room'))],
            'name' => ['required', 'string', 'max:120', Rule::unique('rooms')->ignore($this->route('room'))],
            'floor' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
