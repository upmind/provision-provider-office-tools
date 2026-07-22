<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use UnexpectedValueException;
use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;
use Upmind\ProvisionProviders\OfficeTools\Data\Enums\LoginType;

/**
 * @property-read string $service_id Unique identifier for the service
 * @property-read string $customer_id Unique identifier for the customer
 * @property-read string|null $locale The locale for the session
 * @property-read string|null $login_type Type of Login, one of LoginType enum constant values
 */
class LoginParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'customer_id' => ['required', 'string'],
            'locale' => ['nullable', 'string'],
            'login_type' => ['nullable', 'string', 'in:' . LoginType::stringifyValues()],
        ]);
    }

    public function getLoginTypeEnum(): ?LoginType
    {
        try {
            return LoginType::from($this->login_type);
        } catch (UnexpectedValueException $ex) {
            return null;
        }
    }
}
