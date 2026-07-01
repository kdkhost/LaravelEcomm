<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests\PaymentProvider;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'public_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }
}
