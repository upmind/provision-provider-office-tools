<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Generic result of an email service creation operation.
 *
 * @property-read string $requestId Unique identifier for the creation request
 * @property-read string $serviceId Unique identifier for the created service/order
 * @property-read string $status Status of the created service (e.g., active, pending)
 * @property-read string|null $username Primary username or email for the service
 * @property-read string|null $serviceIdentifier Identifier for the service (e.g., domain)
 * @property-read string|null $packageIdentifier Identifier for the plan/package
 * @property-read array|null $primaryAccount Details of the primary account created
 * @property-read array|null $metadata Provider-specific metadata or additional data
 * @property-read string|null $message Optional success/error message
 */
class ChangePackageResult extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'requestId' => ['required', 'string'],
            'serviceId' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,pending,suspended,deleted,failed,unknown'],
            'message' => ['nullable', 'string'],
        ]);
    }
}
