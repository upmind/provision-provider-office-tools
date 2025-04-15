<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read int $titanOrderId Unique Titan order ID
 * @property-read string|null $reason Reason for account deletion
 */
class ServiceIdentifierParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'serviceId' => ['required', 'integer'],
            'reason' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ]);
    }
}
