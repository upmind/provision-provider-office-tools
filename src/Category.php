<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools;

use Upmind\ProvisionBase\Provider\BaseCategory;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateResult;
use Upmind\ProvisionProviders\OfficeTools\Data\Result;
use Upmind\ProvisionProviders\OfficeTools\Data\ServiceIdentifierParams;


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
            ->setDescription('Provision category which provides professional email services')
            ->setIcon('envelope');
    }

    /**
     * Creates an email service and returns the `username` which can be used to
     * identify the email service in subsequent requests, plus other email service
     * information.
     */
    abstract public function create(CreateParams $params): CreateResult;

    /**
     * Obtain a signed login URL for the service that the system client can redirect to.
     */
    abstract public function login(AccountIdentifierParams $params): LoginResult;

    /**
     * Change the package of an email service.
     */
    abstract public function changePackage(ChangePackageParams $params): Result;

    /**
     * Suspend an email service.
     */
    abstract public function suspend(ServiceIdentifierParams $params): Result;

    /**
     * Unsuspend an email service.
     */
    abstract public function unsuspend(ServiceIdentifierParams $params): Result;

    /**
     * Permanently delete an email service.
     */
    abstract public function terminate(ServiceIdentifierParams $params): Result;
}
