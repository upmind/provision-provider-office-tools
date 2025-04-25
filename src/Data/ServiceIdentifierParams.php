<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $service_id Unique identifier for the service
 * @property-read string $customer_id Unique identifier for the customer
 */
class ServiceIdentifierParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'customer_id' => ['required', 'string'],
            'service_id' => ['required', 'string'],
        ]);
    }
}
