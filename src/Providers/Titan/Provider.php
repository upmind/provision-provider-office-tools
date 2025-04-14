<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan;

use _PHPStan_ce0aaf2bf\Nette\Neon\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
use Upmind\ProvisionProviders\OfficeTools\Providers\Generic\Data\Configuration;

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
            ->setName('Titan Email')
            ->setDescription('Create and manage Titan Email accounts')
            ->setLogoUrl('https://api.upmind.io/images/logos/provision/titan-logo.png');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Upmind\ProvisionBase\Exception\ProvisionFunctionError
     */
    public function create(CreateParams $params): CreateResult
    {
        throw new \Exception('Create is not supported for Titan Email');
        $domain = $params->domain;

        // Map generic params to Titan-specific payload
        $payload = [
            'domainName' => $domain,
            'customerId' => $params->customerId,
            'customerEmailAddress' => $params->customerEmail,
            'planType' => $params->plan,
        ];

        // Map optional fields
        $optionalFields = [
            'customerName' => 'customerName',
            'alternateEmail' => 'alternateEmailAddress',
            'password' => 'password',
            'country' => 'customerCountry',
            'accountCount' => 'noOfAccounts',
            'expiryDate' => 'expiryDate',
            'sendWelcomeEmail' => 'sendWelcomeEmail',
            'primaryAccount' => 'firstEmailAccount',
        ];

        foreach ($optionalFields as $generic => $titan) {
            if (isset($params->$generic)) {
                $payload[$titan] = $params->$generic;
            }
        }

        // Map billing fields
        if (isset($params->billing)) {
            $billingMap = [
                'transactionId' => 'paymentTxnId',
                'amount' => 'chargedAmount',
                'currency' => 'currency',
                'discount' => 'discountAmount',
                'tax' => 'taxAmount',
                'cycle' => 'billingCycle',
            ];

            foreach ($billingMap as $generic => $titan) {
                if (isset($params->billing[$generic])) {
                    $payload[$titan] = $params->billing[$generic];
                }
            }

            if (isset($params->billing['amount']) && $params->billing['currency'] === 'USD') {
                $payload['chargedUSDAmount'] = $params->billing['amount'];
            }
        }

        // Map metadata
        if (isset($params->metadata)) {
            $metadataMap = [
                'source' => 'source',
                'locale' => 'locale',
                'sourceContext' => 'sourceContext',
                'associatedOrderExpiryDate' => 'associatedOrderExpiryDate',
                'associatedOrderPlanType' => 'associatedOrderPlanType',
                'cpanelHostName' => 'cpanelHostName',
                'cpanelUserName' => 'cpanelUserName',
                'partnerBrand' => 'partnerBrand',
                'paymentMethod' => 'paymentMethod',
                'autoRenew' => 'autoRenew',
            ];

            $payload['metadata'] = [];
            foreach ($metadataMap as $generic => $titan) {
                if (isset($params->metadata[$generic])) {
                    $payload['metadata'][$titan] = $params->metadata[$generic];
                }
            }
        }

        try {
            $response = $this->client()->post('partner/createMailOrder', [
                RequestOptions::JSON => $payload,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (!isset($responseData['status']) || $responseData['status'] !== 'active') {
                $this->errorResult('Failed to create Titan email account', $responseData);
            }

            // Map Titan response to generic CreateResult
            $resultData = [
                'requestId' => $responseData['reqID'],
                'serviceId' => (string)$responseData['titanOrderId'], // Convert to string for generality
                'status' => $responseData['status'],
                'username' => $params->customerEmail,
                'serviceIdentifier' => $domain,
                'packageIdentifier' => $params->plan,
                'message' => 'Titan email account created successfully',
            ];

            if (isset($responseData['firstEmailAccountDetails'])) {
                $resultData['primaryAccount'] = [
                    'email' => $params->primaryAccount['email'] ?? null,
                    'loginToken' => $responseData['firstEmailAccountDetails']['webmailAutoLoginToken'] ?? null,
                    'isAdmin' => $params->primaryAccount['isAdmin'] ?? true,
                ];
            }

            return CreateResult::create($resultData);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to create Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string)$e->getResponse()->getBody() : null
            ]);
        }
    }

    protected function client(): Client
    {
        return new Client([
            'base_uri' => rtrim($this->configuration->base_url, '/'),
            RequestOptions::HEADERS => [
                'Authorization' => $this->configuration->auth_token,
                'Content-Type' => 'application/json',
            ],
            RequestOptions::HTTP_ERRORS => false,
            'handler' => $this->getGuzzleHandlerStack()
        ]);
    }

    public function login(AccountIdentifierParams $params): LoginResult
    {
        //throw unsupported exception
        throw new \Exception('Login is not supported for Titan Email');
    }

    public function changePackage(ChangePackageParams $params): Result
    {
        throw new \Exception('changePackage is not supported for Titan Email');
    }

    public function suspend(ServiceIdentifierParams $params): Result
    {
        throw new \Exception('suspend is not supported for Titan Email');
    }

    public function unsuspend(ServiceIdentifierParams $params): Result
    {
        throw new \Exception('unsuspend is not supported for Titan Email');
    }

    public function terminate(ServiceIdentifierParams $params): Result
    {
        throw new \Exception('terminate is not supported for Titan Email');
    }
}
