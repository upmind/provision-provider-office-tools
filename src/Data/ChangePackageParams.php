<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read int providerServiceId Unique provider order ID
 * @property-read string|null serviceId Upminds's order ID
 * @property-read string $planType Provider mail plan type
 * @property-read string|null $paymentTxnId Payment transaction ID
 * @property-read float|null $chargedAmount Charged amount in customer's currency
 * @property-read float|null $chargedUSDAmount Charged amount in USD
 * @property-read float|null $discountAmount Discount amount
 * @property-read float|null $taxAmount Tax amount
 * @property-read string|null $billingCycle Billing cycle (e.g., monthly, yearly)
 * @property-read string|null $expiryDate Expiry date in yyyy-MM-dd format
 * @property-read string|null $currency Currency in ISO code
 * @property-read int|null $noOfAccounts Number of accounts after modification
 * @property-read string|null $action Type of modification being performed
 * @property-read string|null $source Source of the order
 * @property-read string|null $sourceContext Purchase hook for the order
 * @property-read array|null $metadata Metadata for the order
 */
class ChangePackageParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'serviceId' => ['required', 'string'],
            'customerId' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'plan' => ['required', 'string'],
            'expiryDate' => ['nullable', 'string'],
            'billing' => ['nullable', 'array'],
            'billing.transactionId' => ['nullable', 'string'],
            'billing.amount' => ['nullable', 'numeric'],
            'billing.currency' => ['nullable', 'string'], // ISO 4217
            'billing.discount' => ['nullable', 'numeric'],
            'billing.tax' => ['nullable', 'numeric',],
            'billing.cycle' => ['nullable', 'string', 'in:monthly,quarterly,semesterly,yearly,biennial,quadrennial'],
            'numSeats' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:upgrade,addSeats,reduceSeats,renewOrder'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
