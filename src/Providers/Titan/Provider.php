<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\OfficeTools\Category;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateResult;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginParams;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginResult;
use Upmind\ProvisionProviders\OfficeTools\Data\Result;
use Upmind\ProvisionProviders\OfficeTools\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\OfficeTools\Data\UnsuspendResult;
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

    public function create(CreateParams $params): CreateResult
    {
        $domain = $params->domain;

        // Map generic params to Titan-specific payload
        $payload = [
            'domainName' => $domain,
            'customerId' => $params->customerId,
            'customerEmailAddress' => $params->customerEmail,
            'planType' => $params->plan,
            'noOfAccounts' => $params->seatCount,

        ];
        // Map optional fields
        $optionalFields = [
            'customerName' => 'customerName',
            'alternateEmail' => 'alternateEmailAddress',
            'password' => 'password',
            'country' => 'customerCountry',
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

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->errorResult('Failed to create Titan email account', [
                    'statusCode' => $statusCode,
                    'response' => (string)$response->getBody(),
                ]);
            }
            $responseData = json_decode($response->getBody()->getContents(), true);

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
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null
            ]);
        }
    }

    protected function client(): Client
    {


        return new Client([
            'base_uri' => $this->configuration->base_url,
            RequestOptions::HEADERS => [
                'Authorization' => $this->configuration->client_secret,
            ],
            RequestOptions::HTTP_ERRORS => false,
            'handler' => $this->getGuzzleHandlerStack()
        ]);
    }

    public function login(LoginParams $params): LoginResult
    {
        $partnerId = $this->configuration->client_id;
        $titanOrderId = $params->serviceId;
        $expirationTime = time() + 300; // Token valid for 5 minutes

        // Generate JWT payload
        $payload = [
            'titanOrderId' => $titanOrderId,
            'exp' => $expirationTime,
        ];

        // Generate JWT token
        $jwt = JWT::encode($payload, $this->configuration->client_secret, 'HS256');

        // Construct the Titan control panel URL
        $url = $this->configuration->control_panel_url . 'partner/autoLogin?' . http_build_query([
                'partnerId' => $partnerId,
                'jwt' => $jwt,
                'section' => $params->section ?? 'home',
                'action' => $params->action ?? null,
                'email' => $params->email ?? null,
                'locale' => $params->locale ?? 'en-us'
            ]);

        // Return the login result
        return LoginResult::create([
            'url' => $url,
        ]);
    }

    public function changePackage(ChangePackageParams $params): Result
    {
        $serviceId = $params->serviceId;

        // Map generic params to Titan-specific payload
        $payload = [
            'serviceId' => $serviceId,
            'customerId' => $params->customerId,
            'planType' => $params->plan,
            'expiryDate' => $params->expiryDate,
            'domainName' => $params->domain,
        ];

        // Map optional fields
        $optionalFields = [
            'billingCycle' => 'billingCycle',
            'noOfAccounts' => 'noOfAccounts',
            'action' => 'action',
            'source' => 'source',
            'sourceContext' => 'sourceContext',
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
        }

        // Map metadata
        if (isset($params->metadata)) {
            $payload['metadata'] = $params->metadata;
        }

        try {
            $response = $this->client()->post('partner/modifyMailOrder', [
                RequestOptions::JSON => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->errorResult('Failed to modify Titan email account', [
                    'statusCode' => $statusCode,
                    'response' => (string)$response->getBody(),
                ]);
            }
            $responseData = json_decode($response->getBody()->getContents(), true);

            // Map Titan response to generic Result
            $resultData = [
                'requestId' => $responseData['reqID'] ?? null,
                'serviceId' => (string)($responseData['titanOrderId'] ?? $serviceId),
                'status' => $responseData['status'] ?? 'unknown',
                'message' => $responseData['message'] ?? 'Package modified successfully',
            ];

            return Result::create($resultData);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to modify Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null
            ]);
        }
    }

    public function suspend(ServiceIdentifierParams $params): Result
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->serviceId,
            'suspensionType' => $params->reason,
            'note' => $params->note,
        ];

        try {
            // Send the request to the Titan API
            $response = $this->client()->post('partner/suspendMailOrder', [
                RequestOptions::JSON => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->errorResult('Failed to suspend Titan email account', [
                    'statusCode' => $statusCode,
                    'response' => (string)$response->getBody(),
                ]);
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Map Titan response to generic Result
            return Result::create([
                'requestId' => $responseData['reqID'] ?? null,
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to suspend Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null,
            ]);
        }
    }

    public function unsuspend(ServiceIdentifierParams $params): UnsuspendResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->serviceId,
            'suspensionType' => $params->reason,
            'note' => $params->note,
        ];

        try {
            // Send the request to the Titan API
            $response = $this->client()->post('partner/unsuspendMailOrder', [
                RequestOptions::JSON => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->errorResult('Failed to unsuspend Titan email account', [
                    'statusCode' => $statusCode,
                    'response' => (string)$response->getBody(),
                ]);
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Map Titan response to generic Result
            return UnsuspendResult::create([
                'requestId' => $responseData['reqID'] ?? null,
                'status' => $responseData['newStatus'] ?? null,
                'extra' => ['remainingSuspensions' => $responseData['remainingSuspensions']]]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to unsuspend Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null,
            ]);
        }
    }

    public function terminate(ServiceIdentifierParams $params): Result
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->serviceId,
            'reason' => $params->reason,
        ];

        try {
            // Send the request to the Titan API
            $response = $this->client()->post('partner/deleteMailOrder', [
                RequestOptions::JSON => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->errorResult('Failed to terminate Titan email account', [
                    'statusCode' => $statusCode,
                    'response' => (string)$response->getBody(),
                ]);
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Map Titan response to generic Result
            return Result::create([
                'requestId' => $responseData['reqID'] ?? null,
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to terminate Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null,
            ]);
        }
    }
}
