<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $service_id Unique identifier for the service
 * @property-read string|null $reason Reason for operation e.g., account deletion
 * @property-read string|null $note Additional notes or comments
 */
class ServiceIdentifierParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'reason' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ]);
    }
}
