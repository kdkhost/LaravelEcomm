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
            'first_name'   => 'string|required',
            'last_name'    => 'string|required',
            'address1'     => 'string|required',
            'address2'     => 'string|nullable',
            'state'        => 'string|required',
            'neighborhood' => 'string|required',
            'number'       => 'string|required',
            'coupon'       => 'nullable|numeric',
            'phone'        => 'string|required|regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/',
            'post_code'    => 'string|nullable|regex:/^\d{5}-?\d{3}$/',
            'email'        => 'string|required',
            'document'     => ['nullable', new DocumentRule],
            'cart'         => new CartRule,
        ];
    }
}
