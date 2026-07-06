<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $promotionId = $this->route('promotion')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('promotions', 'code')->ignore($promotionId),
            ],
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'minimum_booking_amount' => 'nullable|integer|min:0',
            'maximum_discount' => 'nullable|integer|min:0',
            'usage_quota' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'minimum_booking_amount' => $this->minimum_booking_amount ?? 0,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
