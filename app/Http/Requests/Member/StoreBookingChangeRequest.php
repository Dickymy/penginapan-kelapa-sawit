<?php

namespace App\Http\Requests\Member;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        return $booking && $booking->user_id === $this->user('web')->id && $booking->status === \App\Enums\BookingStatus::Confirmed->value;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:reschedule,room_change,guest_update'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'guest_count' => ['required', 'integer', 'min:1'],
        ];
    }
}
