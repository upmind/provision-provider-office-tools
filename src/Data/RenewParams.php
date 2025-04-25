<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string|null $service_id Service ID
 * @property-read string $customer_id Customer ID
 * @property-read string $domain Domain name
 * @property-read BillingParams $billing Billing metadata
 * @property-read array|null $extra Additional parameters
 */
class RenewParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'billing' => ['required', BillingParams::class],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
