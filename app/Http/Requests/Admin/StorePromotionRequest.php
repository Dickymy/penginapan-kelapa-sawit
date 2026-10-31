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
            'value' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->type === 'percentage' && (int)$value > 100) {
                        $fail('Nilai diskon persentase tidak boleh melebihi 100%.');
                    }
                },
            ],
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'minimum_booking_amount' => 'nullable|integer|min:0',
            'maximum_discount' => 'nullable|integer|min:1',
            'usage_quota' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'show_in_listing' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode promo wajib diisi.',
            'code.unique' => 'Kode promo ini sudah digunakan.',
            'name.required' => 'Nama promo wajib diisi.',
            'type.required' => 'Tipe diskon wajib dipilih.',
            'value.required' => 'Nilai promo wajib diisi.',
            'value.min' => 'Nilai promo minimal 1.',
            'starts_at.required' => 'Tanggal mulai promo wajib diisi.',
            'ends_at.required' => 'Tanggal berakhir promo wajib diisi.',
            'ends_at.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $maxDiscount = $this->maximum_discount;
        if ($maxDiscount === '' || $maxDiscount === null || (int)$maxDiscount <= 0) {
            $maxDiscount = null;
        } else {
            $maxDiscount = (int) $maxDiscount;
        }

        $minAmount = $this->minimum_booking_amount;
        if ($minAmount === '' || $minAmount === null || (int)$minAmount < 0) {
            $minAmount = 0;
        } else {
            $minAmount = (int) $minAmount;
        }

        $this->merge([
            'code' => $this->code ? strtoupper(trim($this->code)) : null,
            'minimum_booking_amount' => $minAmount,
            'maximum_discount' => $maxDiscount,
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
            'show_in_listing' => $this->has('show_in_listing') ? $this->boolean('show_in_listing') : true,
        ]);
    }
}
