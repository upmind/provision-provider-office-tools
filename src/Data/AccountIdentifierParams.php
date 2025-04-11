<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $domainName Domain name for the mail order
 * @property-read string $customerId Customer ID stored on the partner's end
 * @property-read string $customerEmailAddress Customer's email address
 * @property-read string $planType Provider mail plan type
 * @property-read string|null $paymentTxnId Transaction ID for paid plans
 * @property-read float|null $chargedAmount Total charged amount
 * @property-read float|null $chargedUSDAmount Charged amount in USD
 * @property-read float|null $discountAmount Discount amount
 * @property-read float|null $taxAmount Tax amount
 * @property-read string|null $billingCycle Billing cycle (e.g., monthly, quarterly)
 * @property-read string|null $expiryDate Expiry date in yyyy-MM-dd format
 * @property-read string|null $currency Currency in ISO code
 * @property-read int|null $numSeats Number of seats
 * @property-read array|null $extra Additional data
 */
class AccountIdentifierParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'domainName' => ['required', 'string'],
            'customerId' => ['required', 'string'],
            'customerEmailAddress' => ['required', 'email'],
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
            'extra' => ['nullable', 'array'],
        ]);
    }
}