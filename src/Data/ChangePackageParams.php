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
            'providerServiceId' => ['required', 'integer'],
            'serviceId' => ['nullable', 'string'],
            'planType' => ['required', 'string'],
            'paymentTxnId' => ['nullable', 'string'],
            'chargedAmount' => ['nullable', 'numeric'],
            'chargedUSDAmount' => ['nullable', 'numeric'],
            'discountAmount' => ['nullable', 'numeric'],
            'taxAmount' => ['nullable', 'numeric'],
            'billingCycle' => ['nullable', 'string', 'in:1,2,3,4,6,12,24,36,48'],
            'expiryDate' => ['nullable', 'string', 'date_format:Y-m-d'],
            'currency' => ['nullable', 'string', 'size:3'],
            'numSeats' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'in:upgrade,addSeats,reduceSeats,renewOrder'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}