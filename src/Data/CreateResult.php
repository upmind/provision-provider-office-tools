<?php


declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $reqID Request ID
 * @property-read int $titanOrderId Unique order ID for the domain provisioned with Titan
 * @property-read string $status Status of the order (e.g., active, pending, suspended, deleted)
 * @property-read array|null $firstEmailAccountDetails Details of the first email account
 */
class CreateResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'orderId' => ['required', 'integer'],
            'status' => ['required', 'string', 'in:active,pending,suspended,deleted'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}