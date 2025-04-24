<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $request_id Request ID
 */
class UnsuspendResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'request_id' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
