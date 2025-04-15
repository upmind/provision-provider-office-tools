<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $serviceId The unique Titan order ID
 * @property-read string|null $section Control panel section to open (e.g., home, email-accounts)
 * @property-read string|null $action Action to perform within the section
 * @property-read string|null $email Email address for specific actions
 * @property-read string|null $locale Language to set in the control panel (default: en-us)
 */
class LoginParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'serviceId' => ['required', 'string'],
            'section' => ['nullable', 'string', 'in:home,email-accounts,internal-forward,catch-all-email,device-download,configure-desktop,domain-verification,import-email,billing-and-upgrade,buy-email-account'],
            'action' => ['nullable', 'string', 'in:launch-email-creation,paymentSuccess'],
            'email' => ['nullable', 'email'],
            'locale' => ['nullable', 'string', 'size:5'],
        ]);
    }
}
