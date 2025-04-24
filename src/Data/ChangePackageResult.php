<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Generic result of an email service creation operation.
 *
 * @property-read string $request_id Unique identifier for the creation request
 * @property-read string $service_id Unique identifier for the created service/order
 * @property-read string $status Status of the created service (e.g., active, pending)
 */
class ChangePackageResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'request_id' => ['required', 'string'],
            'service_id' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,pending,suspended,deleted,failed,unknown'],
        ]);
    }
}
