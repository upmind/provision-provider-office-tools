<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Billing metadata.
 *
 * @property-read string|null $transaction_id Unique transaction ID
 * @property-read string|float|null $amount Amount charged
 * @property-read string|null $currency Currency code (ISO 4217)
 * @property-read string|float|null $discount Discount amount
 * @property-read string|float|null $tax Tax amount
 * @property-read string|null $cycle Billing cycle (monthly, quarterly, semesterly, yearly, biennial, quadrennial)
 */
class BillingParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'transaction_id' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string'], // ISO 4217
            'discount' => ['nullable', 'numeric'],
            'tax' => ['nullable', 'numeric',],
            'cycle' => ['nullable', 'string', 'in:monthly,quarterly,semesterly,yearly,biennial,quadrennial'],
        ]);
    }
}
