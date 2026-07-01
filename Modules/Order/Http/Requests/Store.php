<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;
use Modules\Order\Rules\CartRule;

class Store extends CoreRequest
{
    /**
     * Shipping information.
     *
     * @var string[]
     */
    public array $shipping;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|CartRule>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'address2' => 'string|nullable',
            'coupon' => 'nullable|numeric',
            'phone' => 'string|required|max:20',
            'post_code' => 'string|nullable',
            'email' => 'string|required',
            'country' => 'string|nullable|max:2',
            'city' => 'string|nullable|max:120',
            'state' => 'string|nullable|max:2',
            'payment_method' => 'nullable|in:cod,paypal,stripe,mercadopago',
            'cart' => new CartRule, // Ensure this custom rule is properly included
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Informe o nome.',
            'last_name.required' => 'Informe o sobrenome.',
            'address1.required' => 'Informe o endereço.',
            'phone.required' => 'Informe o telefone.',
            'email.required' => 'Informe o e-mail.',
            'payment_method.in' => 'Forma de pagamento inválida.',
        ];
    }
}
