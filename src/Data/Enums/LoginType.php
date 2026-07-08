<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\DomainNames\Data\Enums;

use MyCLabs\Enum\Enum;

/**
 * Enum representing different types of Login types.
 *
 * @extends Enum<LoginType::*>
 *
 * @method static LoginType REDIRECT()
 * @method static LoginType TOKEN()
 */
final class LoginType extends Enum
{
    /**
     * Login uses a redirect URL.
     *
     * @var string
     */
    public const REDIRECT = 'redirect';

    /**
     * Login uses a token.
     *
     * @var string
     */
    public const TOKEN = 'token';

    public static function toValues(): array
    {
        return array_values(self::toArray());
    }

    public static function stringifyValues(string $separator = ','): string
    {
        return implode($separator, self::toValues());
    }
}
