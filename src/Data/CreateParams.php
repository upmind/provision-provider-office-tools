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
 */
class CreateParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'domain' => ['required', 'string'],
            'customerId' => ['required', 'string'],
            'customerEmail' => ['required', 'email'],
            'plan' => ['required', 'string'],
            'expiryDate' => ['required', 'string'],
            'seatCount' => ['nullable', 'integer'],
            'customerName' => ['nullable', 'string'],
            'alternateEmail' => ['nullable', 'email'],
            'password' => ['nullable', 'string'],
            'country' => ['nullable', 'string'], // ISO 3166-1 alpha-2
            'billing' => ['nullable', 'array'],
            'billing.transactionId' => ['nullable', 'string'],
            'billing.amount' => ['nullable', 'numeric'],
            'billing.currency' => ['nullable', 'string'], // ISO 4217
            'billing.discount' => ['nullable', 'numeric'],
            'billing.tax' => ['nullable', 'numeric',],
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
