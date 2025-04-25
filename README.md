# [Upmind Provision Providers](https://github.com/upmind) - Office Tools

[![Latest Version on Packagist](https://img.shields.io/packagist/v/upmind/provision-provider-office-tools.svg?style=flat-square)](https://packagist.org/packages/upmind/provision-provider-office-tools)

This provision category contains functions to facilitate basic online service account creation/management including an automatic login feature.

- [Installation](#installation)
- [Usage](#usage)
  - [Quick-start](#quick-start)
- [Supported Providers](#supported-providers)
- [Functions](#functions)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)
- [Upmind](#upmind)

## Installation

```bash
composer require upmind/provision-provider-office-tools
```

## Usage

This library makes use of [upmind/provision-provider-base](https://packagist.org/packages/upmind/provision-provider-base) primitives which we suggest you familiarize yourself with by reading the usage section in the README.

### Quick-start

The easiest way to see this provision category in action and to develop/test changes is to install it in [upmind/provision-workbench](https://github.com/upmind/provision-workbench#readme).

Alternatively you can start using it for your business immediately with [Upmind.com](https://upmind.com/start) - the ultimate web hosting billing and management solution.

**If you wish to develop a new Provider, please refer to the [WORKFLOW](WORKFLOW.md) guide.**

## Supported Providers

The following providers are currently implemented:
  - [TitanMail](https://titanapidocs.docs.apiary.io/#/introduction)

## Functions

| Function | Parameters | Return Data | Description |
|---|---|---|---|
| create() | [_CreateParams_](src/Data/CreateParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Creates a service and returns the `username` which can be used to identify the service in subsequent requests, plus other service information. |
| getInfo() | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Gets info about the service such as plan, status, num_seats etc. |
| login() | [_LoginParams_](src/Data/LoginParams.php) | [_LoginResult_](src/Data/LoginResult.php) | Obtain a signed login URL for the service that the system client can redirect to. |
| renew() | [_RenewParams_](src/Data/RenewParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Renew the service. |
| changePackage() | [_ChangePackageParams_](src/Data/ChangePackageParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Change the package of a service. |
| suspend() | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Suspend a service. |
| unsuspend() | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php) | [_InfoResult_](src/Data/InfoResult.php) | Unsuspend a service. |
| terminate() | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php) | [_EmptyResult_](src/Data/EmptyResult.php) | Permanently delete a service. |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

 - [Nicolás Ramírez](https://github.com/nicolasramirez)
 - [Harry Lewis](https://github.com/uphlewis)
 - [All Contributors](../../contributors)

## License

GNU General Public License version 3 (GPLv3). Please see [License File](LICENSE.md) for more information.

## Upmind

Sell, manage and support web hosting, domain names, ssl certificates, website builders and more with [Upmind.com](https://upmind.com/start) - the ultimate web hosting billing and management solution.
