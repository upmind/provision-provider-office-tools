<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Generic parameters for creating an email service account.
 *
 * @property-read string $domain Domain name for the email service
 * @property-read string $customerId Unique identifier for the customer
 * @property-read string $customerEmail Primary email address for the customer
 * @property-read string $plan Identifier for the service plan or package
 * @property-read string|null $customerName Customer's full name
 * @property-read string|null $alternateEmail Secondary contact email
 * @property-read string|null $password Initial password for the account
 * @property-read string|null $country Customer's country code (e.g., ISO 3166-1 alpha-2)
 * @property-read int|null $accountCount Number of email accounts to provision
 * @property-read string|null $expiryDate Account or subscription expiry date
 * @property-read array|null $billing Billing-related details (e.g., transaction ID, amount, currency)
 * @property-read array|null $metadata Provider-specific metadata or custom fields
 * @property-read array|null $primaryAccount Details for the first email account to create
 * @property-read bool|null $sendWelcomeEmail Whether to send a welcome email
 */
class CreateParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'domain' => ['required', 'string', 'domain'],
            'customerId' => ['required', 'string'],
            'customerEmail' => ['required', 'email'],
            'plan' => ['required', 'string'],
            'customerName' => ['nullable', 'string'],
            'alternateEmail' => ['nullable', 'email'],
            'password' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'regex:/^[A-Z]{2}$/'], // ISO 3166-1 alpha-2
            'accountCount' => ['nullable', 'integer', 'min:1'],
            'expiryDate' => ['nullable', 'string', 'date_format:Y-m-d'],
            'billing' => ['nullable', 'array'],
            'billing.transactionId' => ['nullable', 'string'],
            'billing.amount' => ['nullable', 'numeric', 'min:0'],
            'billing.currency' => ['nullable', 'string', 'regex:/^[A-Z]{3}$/'], // ISO 4217
            'billing.discount' => ['nullable', 'numeric', 'min:0'],
            'billing.tax' => ['nullable', 'numeric', 'min:0'],
            'billing.cycle' => ['nullable', 'string', 'in:monthly,quarterly,semesterly,yearly,biennial,quadrennial'],
            'metadata' => ['nullable', 'array'],
            'primaryAccount' => ['nullable', 'array'],
            'primaryAccount.email' => ['required_with:primaryAccount', 'email'],
            'primaryAccount.password' => ['required_with:primaryAccount', 'string'],
            'primaryAccount.name' => ['nullable', 'string'],
            'primaryAccount.isAdmin' => ['nullable', 'boolean'],
            'primaryAccount.alternateEmail' => ['nullable', 'email'],
            'sendWelcomeEmail' => ['nullable', 'boolean'],
        ]);
    }
}
