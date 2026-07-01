<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;
use Modules\Order\Rules\CartRule;
use Modules\Order\Rules\DocumentRule;

class Store extends CoreRequest
{
    public array $shipping;

    public function rules(): array
    {
        return [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'address2' => 'string|nullable',
            'coupon' => 'nullable|numeric',
            'phone' => 'numeric|required',
            'post_code' => 'string|nullable',
            'email' => 'string|required',
            'document' => ['nullable', new DocumentRule],
            'cart' => new CartRule,
        ];
    }
}
