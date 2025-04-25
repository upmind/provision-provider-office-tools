<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Service information.
 *
 * @property-read string $customer_id Service customer ID
 * @property-read string $service_id Service ID
 * @property-read string $domain Service domain
 * @property-read string $plan Service plan
 * @property-read int $seat_count Total number of seats
 * @property-read int $seat_count_used Number of seats used
 * @property-read string|null $status Service status
 * @property-read string|null $expiry_date Service expiry date
 * @property-read array|null $metadata Additional metadata
 */
class InfoResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'customer_id' => ['required', 'string'],
            'service_id' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'plan' => ['required', 'string'],
            'seat_count' => ['required', 'integer'],
            'seat_count_used' => ['required', 'integer'],
            'status' => ['nullable', 'string'],
            'expiry_date' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ]);
    }
}
