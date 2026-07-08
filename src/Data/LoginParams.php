<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use UnexpectedValueException;
use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;
use Upmind\ProvisionProviders\DomainNames\Data\Enums\LoginType;

/**
 * @property-read string $service_id Unique identifier for the service
 * @property-read string $customer_id Unique identifier for the customer
 * @property-read string|null $locale The locale for the session
 * @property-read string|null $type Type of Login, one of LoginType enum constant values
 */
class LoginParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'locale' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:' . LoginType::stringifyValues()],
        ]);
    }

    public function getLoginType(): ?LoginType
    {
        try {
            return LoginType::from($this->type);
        } catch (UnexpectedValueException $ex) {
            return null;
        }
    }
}
