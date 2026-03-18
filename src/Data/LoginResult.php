<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $type Login type for the service
 * @property-read string|null $redirect_url Login Redirect URL
 * @property-read string|null $token Control Panel Token
 */
class LoginResult extends ResultData
{
    /**
     * Login uses a redirect URL.
     *
     * @var string
     */
    public const TYPE_REDIRECT = 'redirect';

    /**
     * Login uses a token.
     *
     * @var string
     */
    public const TYPE_TOKEN = 'token';

    public const VALID_TYPES = [
        self::TYPE_REDIRECT,
        self::TYPE_TOKEN,
    ];

    public static function rules(): Rules
    {
        return new Rules([
            'type' => ['required', 'in:' . implode(',', self::VALID_TYPES)],
            'redirect_url' => [
                'required_if:type,' . self::TYPE_REDIRECT,
                'nullable',
                'url',
            ],
            'token' => [
                'required_if:type,' . self::TYPE_TOKEN,
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
    public function setRedirectUrl(?string $url): self
    {
        $this->setValue('redirect_url', $url);

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
