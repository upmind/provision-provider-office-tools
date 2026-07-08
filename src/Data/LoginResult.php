<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;
use Upmind\ProvisionProviders\DomainNames\Data\Enums\LoginType;

/**
 * @property-read string $type Login type for the service, must be one of LoginType enum constants.
 * @property-read string|null $url Login Redirect URL
 * @property-read string|null $token Control Panel Token
 */
class LoginResult extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'type' => ['required', 'in:' . LoginType::stringifyValues()],
            'url' => [
                'required_if:type,' . LoginType::REDIRECT,
                'nullable',
                'url',
            ],
            'token' => [
                'required_if:type,' . LoginType::TOKEN,
                'nullable',
                'string'
            ],
        ]);
    }

    /**
     * @return self $this
     */
    public function setType(string $type): self
    {
        $this->setValue('type', $type);

        return $this;
    }

    /**
     * @return self $this
     */
    public function setUrl(?string $url): self
    {
        $this->setValue('url', $url);

        return $this;
    }

    /**
     * @return self $this
     */
    public function setToken(?string $token): self
    {
        $this->setValue('token', $token);

        return $this;
    }
}
