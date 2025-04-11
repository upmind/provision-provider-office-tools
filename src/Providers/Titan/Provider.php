<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan;

use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\OfficeTools\Category;
use Upmind\ProvisionProviders\OfficeTools\Data\AccountIdentifierParams;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateResult;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginResult;
use Upmind\ProvisionProviders\OfficeTools\Data\Result;
use Upmind\ProvisionProviders\OfficeTools\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\OfficeTools\Providers\Titan\Data\Configuration;

class Provider extends Category implements ProviderInterface
{
    protected Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('Titan')
            ->setDescription('Titan email provider')
            ->setLogoUrl('https://api.upmind.io/images/logos/provision/titan-logo_2x.png');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function create(CreateParams $params): CreateResult
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function login(AccountIdentifierParams $params): LoginResult
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function suspend(ServiceIdentifierParams $params): Result
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function unsuspend(ServiceIdentifierParams $params): Result
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function changePackage(ChangePackageParams $params): Result
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function renew(AccountIdentifierParams $params): Result
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }

    /**
     * @throws ProvisionFunctionError
     */
    public function terminate(ServiceIdentifierParams $params): Result
    {
        throw new ProvisionFunctionError('Operation not supported for Titan email provider');
    }
}
