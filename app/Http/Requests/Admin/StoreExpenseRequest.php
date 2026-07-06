<?php

namespace App\Http\Requests\Admin;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_date' => ['required', 'date'],
            'category' => ['required', Rule::in(Expense::CATEGORIES)],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
        ];
    }
}
