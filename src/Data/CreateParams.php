<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Generic parameters for creating an email service account.
 *
 * @property-read string $domain Domain name for the email service
 * @property-read string $customer_id Unique identifier for the customer
 * @property-read string $customer_email Customer's email address
 * @property-read string $plan Plan name for the service
 * @property-read int $seat_count Number of seats
 * @property-read string|null $customer_name Customer's name
 * @property-read string|null $country_code Country code (ISO 3166-1 alpha-2)
 * @property-read BillingParams $billing Billing metadata
 * @property-read array|null $metadata Additional metadata for the service
 */
class CreateParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'domain' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
            'plan' => ['required', 'string'],
            'seat_count' => ['required', 'integer'],
            'customer_name' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string'], // ISO 3166-1 alpha-2
            'billing' => ['required', BillingParams::class],
            'metadata' => ['nullable', 'array'],
        ]);
    }
}
