<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $url Login URL for the service
 */
class LoginResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'url' => ['required', 'string'],
        ]);
    }
}
