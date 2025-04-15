<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Generic\Data;

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
            'client_secret' => ['required', 'string'],
            'client_id' => ['required', 'string'],
            'base_url' => ['required', 'url'],
            'has_create' => ['boolean'],
            'create_endpoint_http_method' => ['required_if:has_create,1', 'string', 'in:post,put,patch,get'],
            'create_endpoint_url' => ['required_if:has_create,1', 'string'],
            'login_endpoint_http_method' => ['required', 'string', 'in:post,put,patch,get'],
            'login_endpoint_url' => ['required', 'url'],
            'has_suspend' => ['boolean'],
            'suspend_endpoint_http_method' => ['required_if:has_suspend,1', 'string', 'in:post,put,patch,get,delete'],
            'suspend_endpoint_url' => ['required_if:has_suspend,1', 'url'],
            'unsuspend_endpoint_http_method' => ['required_if:has_suspend,1', 'string', 'in:post,put,patch,get,delete'],
            'unsuspend_endpoint_url' => ['required_if:has_suspend,1', 'url'],
            'has_change_package' => ['boolean'],
            'change_package_endpoint_http_method' => ['required_if:has_change_package,1', 'string', 'in:post,put,patch,get,delete'],
            'change_package_endpoint_url' => ['required_if:has_change_package,1', 'url'],
            'has_terminate' => ['boolean'],
            'terminate_endpoint_http_method' => ['required_if:has_terminate,1', 'string', 'in:post,put,patch,get,delete'],
            'terminate_endpoint_url' => ['required_if:has_terminate,1', 'url'],
            'control_panel_url' => ['required', 'url'],
        ]);
    }
}
