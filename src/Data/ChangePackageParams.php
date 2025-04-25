<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string|null $service_id Upminds's order ID
 * @property-read string $customer_id Customer ID
 * @property-read string $domain Domain name
 * @property-read string $plan Plan name
 * @property-read string|null $expiry_date Expiry date
 * @property-read BillingParams|null $billing Billing metadata
 * @property-read int|null $num_seats Number of seats
 * @property-read string|null $action Action to perform (upgrade, addSeats, reduceSeats, renewOrder)
 * @property-read array|null $extra Additional parameters
 */
class ChangePackageParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'plan' => ['required', 'string'],
            'expiry_date' => ['nullable', 'string'],
            'billing' => ['nullable', BillingParams::class],
            'num_seats' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:upgrade,addSeats,reduceSeats,renewOrder'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
