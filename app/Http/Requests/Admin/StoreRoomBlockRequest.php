<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],
            'is_indefinite' => ['nullable', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'required_unless:is_indefinite,1', 'date', 'after:start_date'],
            'reason_type' => ['required', 'string'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
