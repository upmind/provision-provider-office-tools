<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools;

use Upmind\ProvisionBase\Provider\BaseCategory;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginParams;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageParams;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageResult;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateResult;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginResult;
use Upmind\ProvisionProviders\OfficeTools\Data\EmptyResult;
use Upmind\ProvisionProviders\OfficeTools\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\OfficeTools\Data\UnsuspendResult;

/**
 * This provision category contains functions to facilitate basic online service
 * email service creation/management including an automatic login feature.
 */
abstract class Category extends BaseCategory
{
    public static function aboutCategory(): AboutData
    {
        return AboutData::create()
            ->setName('Office Tools')
            ->setDescription('Provision category for office tools such as hosted email')
            ->setIcon('envelope');
    }

    /**
     * Creates a service and returns the `username` which can be used to
     * identify the service in subsequent requests, plus other service
     * information.
     */
    abstract public function create(CreateParams $params): CreateResult;

    /**
     * Obtain a signed login URL for the service that the system client can redirect to.
     */
    abstract public function login(LoginParams $params): LoginResult;

    /**
     * Change the package of a service.
     */
    abstract public function changePackage(ChangePackageParams $params): ChangePackageResult;

    /**
     * Suspend a service.
     */
    abstract public function suspend(ServiceIdentifierParams $params): EmptyResult;

    /**
     * Unsuspend a service.
     */
    abstract public function unsuspend(ServiceIdentifierParams $params): UnsuspendResult;

    /**
     * Permanently delete a service.
     */
    abstract public function terminate(ServiceIdentifierParams $params): EmptyResult;
}
