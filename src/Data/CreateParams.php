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
 * @property-read string $expiry_date Expiry date for the service
 * @property-read int|null $seat_count Number of seats
 * @property-read string|null $customer_name Customer's name
 * @property-read string|null $alternate_email Alternate email address for the customer
 * @property-read string|null $password Password for the primary account
 * @property-read string|null $country Country code (ISO 3166-1 alpha-2)
 * @property-read BillingParams|null $billing Billing metadata
 * @property-read array|null $metadata Additional metadata for the service
 * @property-read array|null $primary_account Details of the primary account
 * @property-read bool|null $send_welcome_email Whether to send a welcome email
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
            'expiry_date' => ['required', 'string'],
            'seat_count' => ['nullable', 'integer'],
            'customer_name' => ['nullable', 'string'],
            'alternate_email' => ['nullable', 'email'],
            'password' => ['nullable', 'string'],
            'country' => ['nullable', 'string'], // ISO 3166-1 alpha-2
            'billing' => ['nullable', BillingParams::class],
            'metadata' => ['nullable', 'array'],
            'primary_account' => ['nullable', 'array'],
            'primary_account.email' => ['required_with:primary_account', 'email'],
            'primary_account.password' => ['required_with:primary_account', 'string'],
            'primary_account.name' => ['nullable', 'string'],
            'primary_account.is_admin' => ['nullable', 'boolean'],
            'primary_account.alternate_email' => ['nullable', 'email'],
            'send_welcome_email' => ['nullable', 'boolean'],
        ]);
    }
}
