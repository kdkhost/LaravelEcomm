<?php

declare(strict_types=1);

namespace Modules\Order\Rules;

use Illuminate\Contracts\Validation\Rule;

class DocumentRule implements Rule
{
    private string \ = 'cpf/cnpj';

    public function passes(\, \): bool
    {
        \ = preg_replace('/\D/', '', \);

        if (strlen(\) === 11) {
            \->type = 'CPF';
            return \->validateCpf(\);
        }

        if (strlen(\) === 14) {
            \->type = 'CNPJ';
            return \->validateCnpj(\);
        }

        return false;
    }

    public function message(): string
    {
        return "O :attribute informado não é um {\->type} válido.";
    }

    private function validateCpf(string \): bool
    {
        if (preg_match('/^(\d)\1{10}$/', \)) {
            return false;
        }

        \ = 0;
        for (\ = 0; \ < 9; \++) {
            \ += (int) \[\] * (10 - \);
        }
        \ = \ % 11;
        \ = \ < 2 ? 0 : 11 - \;

        if ((int) \[9] !== \) {
            return false;
        }

        \ = 0;
        for (\ = 0; \ < 10; \++) {
            \ += (int) \[\] * (11 - \);
        }
        \ = \ % 11;
        \ = \ < 2 ? 0 : 11 - \;

        return (int) \[10] === \;
    }

    private function validateCnpj(string \): bool
    {
        if (preg_match('/^(\d)\1{13}$/', \)) {
            return false;
        }

        \ = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        \ = 0;
        for (\ = 0; \ < 12; \++) {
            \ += (int) \[\] * \[\];
        }
        \ = \ % 11;
        \ = \ < 2 ? 0 : 11 - \;

        if ((int) \[12] !== \) {
            return false;
        }

        \ = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        \ = 0;
        for (\ = 0; \ < 13; \++) {
            \ += (int) \[\] * \[\];
        }
        \ = \ % 11;
        \ = \ < 2 ? 0 : 11 - \;

        return (int) \[13] === \;
    }
}