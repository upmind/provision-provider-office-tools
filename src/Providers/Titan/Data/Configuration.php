<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Configuration class for Titan Mail API
 *
 * @property-read string $api_url Base URL of the Titan API
 * @property-read string $client_id API client ID
 * @property-read string $client_secret Authentication token for API access
 * @property-read string $control_panel_url URL for login redirects
 * @property-read bool $send_welcome_email Flag to send a Titan welcome email on create
 * @property-read string|null $login_section Section to redirect to after login
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'api_url' => ['required', 'url'],
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
            'control_panel_url' => ['required', 'url'],
            'send_welcome_email' => ['nullable', 'boolean'],
            'login_section' => ['nullable', 'string', 'in:home,email-accounts,internal-forward,catch-all-email,device-download,configure-desktop,domain-verification,import-email,billing-and-upgrade,buy-email-account'],
        ]);
    }
}
