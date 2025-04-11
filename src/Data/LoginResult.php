<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $reqID Request ID
 */
class LoginResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'requestId' => ['nullable', 'string'],
        ]);
    }
}