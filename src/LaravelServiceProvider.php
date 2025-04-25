<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools;

use Upmind\ProvisionBase\Laravel\ProvisionServiceProvider;
use Upmind\ProvisionProviders\OfficeTools\Providers\Titan\Provider as TitanProvider;

class LaravelServiceProvider extends ProvisionServiceProvider
{
    public function boot()
    {
        $this->bindCategory('office-tools', Category::class);

        $this->bindProvider('office-tools', 'titan-mail', TitanProvider::class);
    }
}
