<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Generic result of an email service creation operation.
 *
 * @property-read string $request_id Unique identifier for the creation request
 * @property-read string $service_id Unique identifier for the created service/order
 * @property-read string $status Status of the created service (e.g., active, pending)
 * @property-read string|null $username Primary username or email for the service
 * @property-read string|null $service_identifier Identifier for the service (e.g., domain)
 * @property-read string|null $package_identifier Identifier for the plan/package
 * @property-read array|null $primary_account Details of the primary account created
 * @property-read array|null $metadata Provider-specific metadata or additional data
 * @property-read string|null $message Optional success/error message
 */
class CreateResult extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'request_id' => ['required', 'string'],
            'service_id' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,pending,suspended,deleted,failed'],
            'username' => ['nullable', 'string'],
            'service_identifier' => ['nullable', 'string'],
            'package_identifier' => ['nullable', 'string'],
            'primary_account' => ['nullable', 'array'],
            'primary_account.email' => ['nullable', 'email'],
            'primary_account.login_token' => ['nullable', 'string'],
            'primary_account.is_admin' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
            'message' => ['nullable', 'string'],
        ]);
    }
}
