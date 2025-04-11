<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\AutoLogin\Providers\Titan\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Configuration class for Titan Mail API
 *
 * @property-read string $api_url Base URL of the Titan API
 * @property-read string $api_token Authentication token for API access
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'api_url' => ['required', 'url'],
            'client_secret' => ['required', 'string'],
            'client_id' => ['required', 'string'],
        ]);
    }
}
