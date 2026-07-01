<?php

declare(strict_types=1);

namespace Modules\Cron\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCronJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'command' => 'required|string|max:255',
            'frequency' => 'required|string|in:everyMinute,everyFiveMinutes,everyTenMinutes,everyFifteenMinutes,everyThirtyMinutes,hourly,everyTwoHours,everyThreeHours,everyFourHours,everySixHours,daily,weekly',
            'params' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
