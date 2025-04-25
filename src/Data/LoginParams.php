<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $service_id Unique identifier for the service
 * @property-read string $customer_id Unique identifier for the customer
 * @property-read string|null $locale The locale for the session
 */
class LoginParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'locale' => ['nullable', 'string'],
        ]);
    }
}
