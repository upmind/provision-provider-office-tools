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
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageResult;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateResult;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginParams;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginResult;
use Upmind\ProvisionProviders\OfficeTools\Data\EmptyResult;
use Upmind\ProvisionProviders\OfficeTools\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\OfficeTools\Data\UnsuspendResult;
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
            ->setName('Titan Email')
            ->setDescription('Create and manage Titan Email accounts')
            ->setLogoUrl('https://api.upmind.io/images/logos/provision/titan-mail-logo.png');
    }

    public function create(CreateParams $params): CreateResult
    {
        $domain = $params->domain;

        // Map generic params to Titan-specific payload
        $payload = [
            'domainName' => $domain,
            'customerId' => $params->customer_id,
            'customerEmailAddress' => $params->customer_email,
            'planType' => $params->plan,
            'noOfAccounts' => $params->seat_count,

        ];
        // Map optional fields
        $optionalFields = [
            'customer_name' => 'customerName',
            'alternate_email' => 'alternateEmailAddress',
            'password' => 'password',
            'country' => 'customerCountry',
            'expiry_date' => 'expiryDate',
            'send_welcome_email' => 'sendWelcomeEmail',
        ];

        foreach ($optionalFields as $generic => $titan) {
            if (isset($params->$generic)) {
                $payload[$titan] = $params->$generic;
            }
        }

        // Map billing fields
        if (isset($params->billing)) {
            $billingMap = [
                'transaction_id' => 'paymentTxnId',
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

        // Map primary account fields
        if (isset($params->primary_account)) {
            $primaryAccountMap = [
                'email' => 'email',
                'password' => 'password',
                'name' => 'name',
                'is_admin' => 'isAdmin',
                'alternate_email' => 'alternateEmail',
            ];

            foreach ($primaryAccountMap as $generic => $titan) {
                if (isset($params->primary_account[$generic])) {
                    $payload['firstEmailAccount'][$titan] = $params->primary_account[$generic];
                }
            }
        }

        // Map metadata
        if (isset($params->metadata)) {
            $metadataMap = [
                'source' => 'source',
                'locale' => 'locale',
                'source_context' => 'sourceContext',
                'associated_order_expiry_date' => 'associatedOrderExpiryDate',
                'associated_order_plan_type' => 'associatedOrderPlanType',
                'cpanel_host_name' => 'cpanelHostName',
                'cpanel_user_name' => 'cpanelUserName',
                'partner_brand' => 'partnerBrand',
                'payment_method' => 'paymentMethod',
                'auto_renew' => 'autoRenew',
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
                'request_id' => $responseData['reqID'],
                'service_id' => (string)$responseData['titanOrderId'], // Convert to string for generality
                'status' => $responseData['status'],
                'username' => $params->customer_email,
                'service_identifier' => $domain,
                'package_identifier' => $params->plan,
                'message' => 'Titan email account created successfully',
            ];

            if (isset($responseData['firstEmailAccountDetails'])) {
                $resultData['primary_account'] = [
                    'email' => $params->primary_account['email'] ?? null,
                    'login_token' => $responseData['firstEmailAccountDetails']['webmailAutoLoginToken'] ?? null,
                    'is_admin' => $params->primary_account['is_admin'] ?? true,
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
            'base_uri' => $this->configuration->api_url,
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
        $titanOrderId = $params->service_id;
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

    public function changePackage(ChangePackageParams $params): ChangePackageResult
    {
        $serviceId = $params->service_id;

        // Map generic params to Titan-specific payload
        $payload = [
            'serviceId' => $serviceId,
            'customerId' => $params->customer_id,
            'planType' => $params->plan,
            'expiryDate' => $params->expiry_date,
            'domainName' => $params->domain,
        ];

        // Map optional fields
        $optionalFields = [
            'billing_cycle' => 'billingCycle',
            'num_seats' => 'noOfAccounts',
            'action' => 'action',
            'source' => 'source',
            'source_context' => 'sourceContext',
        ];

        foreach ($optionalFields as $generic => $titan) {
            if (isset($params->$generic)) {
                $payload[$titan] = $params->$generic;
            }
        }

        // Map billing fields
        if (isset($params->billing)) {
            $billingMap = [
                'transaction_id' => 'paymentTxnId',
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
                'request_id' => $responseData['reqID'] ?? null,
                'service_id' => (string)($responseData['titanOrderId'] ?? $serviceId),
                'status' => $responseData['status'] ?? 'unknown',
            ];

            return ChangePackageResult::create($resultData)
                ->setMessage($responseData['message'] ?? 'Package modified successfully');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to modify Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null
            ]);
        }
    }

    public function suspend(ServiceIdentifierParams $params): EmptyResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->service_id,
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
            return EmptyResult::create([
                'request_id' => $responseData['reqID'] ?? null,
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
            'titanOrderId' => $params->service_id,
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
                'request_id' => $responseData['reqID'] ?? null,
                'status' => $responseData['newStatus'] ?? null,
                'extra' => ['remainingSuspensions' => $responseData['remainingSuspensions']]]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to unsuspend Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null,
            ]);
        }
    }

    public function terminate(ServiceIdentifierParams $params): EmptyResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->service_id,
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
            return EmptyResult::create([
                'request_id' => $responseData['reqID'] ?? null,
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->errorResult('Failed to terminate Titan email account', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody() : null,
            ]);
        }
    }
}
