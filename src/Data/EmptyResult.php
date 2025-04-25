<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * No result data necessary.
 */
class EmptyResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            //
        ]);
    }
}
