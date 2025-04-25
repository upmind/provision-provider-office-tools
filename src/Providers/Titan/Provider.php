<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use Throwable;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\OfficeTools\Category;
use Upmind\ProvisionProviders\OfficeTools\Data\ChangePackageParams;
use Upmind\ProvisionProviders\OfficeTools\Data\CreateParams;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginParams;
use Upmind\ProvisionProviders\OfficeTools\Data\LoginResult;
use Upmind\ProvisionProviders\OfficeTools\Data\EmptyResult;
use Upmind\ProvisionProviders\OfficeTools\Data\InfoResult;
use Upmind\ProvisionProviders\OfficeTools\Data\RenewParams;
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
            ->setName('Titan Email')
            ->setDescription('Create and manage Titan Email accounts')
            ->setLogoUrl('https://api.upmind.io/images/logos/provision/titan-mail-logo.png');
    }

    public function create(CreateParams $params): InfoResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'domainName' => $params->domain,
            'customerId' => $params->customer_id,
            'customerName' => $params->customer_name,
            'customerEmailAddress' => $params->customer_email,
            'alternateEmailAddress' => $params->customer_email,
            'customerCountry' => $params->country_code,
            'planType' => $params->plan,
            'noOfAccounts' => $params->seat_count,
            'send_welcome_email' => boolval($this->configuration->send_welcome_email),
            'chargedAmount' => $params->billing->amount,
            'currency' => $params->billing->currency,
            'expiryDate' => $params->billing->expiry_date
                ?? Carbon::now()->addMonths($params->billing->billing_cycle_months)->format('Y-m-d'),
        ];

        // Map optional billing fields
        $billing = $params->billing;

        if ($billing->transaction_id) {
            $payload['paymentTxnId'] = $billing->transaction_id;
        }
        if ($billing->discount) {
            $payload['discountAmount'] = $billing->discount;
        }
        if ($billing->tax) {
            $payload['taxAmount'] = $billing->tax;
        }

        if ($cycle = $this->billingCycleMonthsToCycle($billing->billing_cycle_months)) {
            $payload['billingCycle'] = $cycle;
        }

        $responseData = $this->apiRequest('POST', 'partner/createMailOrder', $payload);

        return $this->getInfoResult($params->customer_id, (string)$responseData['titanOrderId'])
            ->setMessage('Titan email account created successfully');
    }

    public function getInfo(ServiceIdentifierParams $params): InfoResult
    {
        return $this->getInfoResult($params->customer_id, $params->service_id);
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
        $url = rtrim($this->configuration->control_panel_url, '/') . '/partner/autoLogin?' . http_build_query([
            'partnerId' => $partnerId,
            'jwt' => $jwt,
            'section' => $this->configuration->login_section ?? 'home',
            'locale' => $params->locale ?? 'en-us',
        ]);

        // Return the login result
        return LoginResult::create([
            'url' => $url,
        ]);
    }

    public function renew(RenewParams $params): InfoResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'serviceId' => $params->service_id,
            'customerId' => $params->customer_id,
            'domainName' => $params->domain,
            'chargedAmount' => $params->billing->amount,
            'currency' => $params->billing->currency,
            'expiryDate' => $params->billing->expiry_date
                ?? Carbon::now()->addMonths($params->billing->billing_cycle_months)->format('Y-m-d'),
        ];

        if ($cycle = $this->billingCycleMonthsToCycle($params->billing->billing_cycle_months)) {
            $payload['billingCycle'] = $cycle;
        }

        $this->apiRequest('POST', 'partner/modifyMailOrder', $payload);

        return $this->getInfoResult($params->customer_id, $params->service_id)
            ->setMessage('Service updated successfully');
    }

    public function changePackage(ChangePackageParams $params): InfoResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'serviceId' => $params->service_id,
            'customerId' => $params->customer_id,
            'domainName' => $params->domain,
            'planType' => $params->plan,
            'noOfAccounts' => $params->seat_count,
            'chargedAmount' => $params->billing->amount,
            'currency' => $params->billing->currency,
            'expiryDate' => $params->billing->expiry_date
                ?? Carbon::now()->addMonths($params->billing->billing_cycle_months)->format('Y-m-d'),
        ];

        if ($cycle = $this->billingCycleMonthsToCycle($params->billing->billing_cycle_months)) {
            $payload['billingCycle'] = $cycle;
        }

        $this->apiRequest('POST', 'partner/modifyMailOrder', $payload);

        return $this->getInfoResult($params->customer_id, $params->service_id)
            ->setMessage('Service updated successfully');
    }

    public function suspend(ServiceIdentifierParams $params): InfoResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->service_id,
        ];

        $this->apiRequest('POST', 'partner/suspendMailOrder', $payload);

        return $this->getInfoResult($params->customer_id, $params->service_id)
            ->setMessage('Service suspended successfully');
    }

    public function unsuspend(ServiceIdentifierParams $params): InfoResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->service_id,
        ];

        $this->apiRequest('POST', 'partner/unsuspendMailOrder', $payload);

        return $this->getInfoResult($params->customer_id, $params->service_id)
            ->setMessage('Service unsuspended successfully');
    }

    public function terminate(ServiceIdentifierParams $params): EmptyResult
    {
        // Map generic params to Titan-specific payload
        $payload = [
            'titanOrderId' => $params->service_id,
        ];

        $this->apiRequest('POST', 'partner/deleteMailOrder', $payload);

        return EmptyResult::create()
            ->setMessage('Service terminated');
    }

    protected function getInfoResult(string $customerId, string $serviceId): InfoResult
    {
        $payload = [
            'customerId' => $customerId,
        ];

        try {
            $responseData = $this->apiRequest('GET', 'partner/listMailOrders', $payload);
        } catch (ProvisionFunctionError $e) {
            $errorCode = $e->getData()['response']['code'] ?? null;

            if ($errorCode !== 'UserNotFound') {
                throw $e;
            }

            // customer not found; try to determine correct customer id

            $customerId = $this->findCustomerIdByServiceId($serviceId);

            $payload = [
                'customerId' => $customerId
            ];

            $responseData = $this->apiRequest('GET', 'partner/listMailOrders', $payload);
        }

        $order = null;

        foreach ($responseData['orders'] as $orderData) {
            if ((string)$orderData['titanOrderId'] === (string)$serviceId) {
                $order = $orderData;
            }
        }

        if ($order === null) {
            $this->errorResult('Customer service not found', [
                'customer_id' => $customerId,
                'service_id' => $serviceId,
            ]);
        }

        $resultData = [
            'customer_id' => $customerId,
            'service_id' => (string)$order['titanOrderId'],
            'domain' => $order['domainName'],
            'plan' => $order['planType'],
            'status' => $order['status'],
            'seat_count' => (int)$order['noOfAccounts'],
            'seat_count_used' => (int)$order['noOfActiveAccounts'],
            'expiry_date' => $order['expiryDate'] ?? null,
        ];

        return InfoResult::create($resultData);
    }

    /**
     * Attempt to determine customer ID by listing email accounts for the given service id.
     */
    protected function findCustomerIdByServiceId(string $serviceId): string
    {
        $payload = [
            'titanOrderId' => $serviceId,
        ];

        $responseData = $this->apiRequest('GET', 'partner/listEmailAccounts', $payload);

        foreach ($responseData['accounts'] as $emailData) {
            return (string)$emailData['customerId'];
        }

        $this->errorResult('Customer ID not found', [
            'service_id' => $serviceId,
        ]);
    }

    /**
     * @param int|string|null $months
     */
    protected function billingCycleMonthsToCycle($months): ?string
    {
        switch ($months) {
            case 1:
                return 'monthly';
            case 3:
                return 'quarterly';
            case 6:
                return 'semesterly';
            case 12:
                return 'yearly';
            case 24:
                return 'biennial';
            case 48:
                return 'quadrennial';
            default:
                return null;
        }
    }

    protected function client(): Client
    {
        return new Client([
            'base_uri' => $this->configuration->api_url,
            RequestOptions::HEADERS => [
                'Authorization' => $this->configuration->client_secret,
            ],
            'handler' => $this->getGuzzleHandlerStack()
        ]);
    }

    /**
     * @throws ProvisionFunctionError
     *
     * @return array<string,mixed>
     */
    protected function apiRequest(string $method, string $uri, array $payload): array
    {
        $requestOptions = [];

        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                $requestOptions = [
                    RequestOptions::JSON => $payload,
                ];
                break;
            default:
                $requestOptions = [
                    RequestOptions::QUERY => $payload,
                ];
                break;
        }

        try {
            $response = $this->client()->request($method, $uri, $requestOptions);
            $body = (string)$response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($data === null) {
                $this->errorResult('Unexpected Provider API Response', [
                    'http_code' => $response->getStatusCode(),
                    'response' => $body,
                ]);
            }

            return $data;
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * @throws ProvisionFunctionError
     *
     * @return no-return
     */
    protected function handleException(Throwable $e): void
    {
        if ($e instanceof TransferException) {
            if ($e instanceof RequestException) {
                if ($response = $e->getResponse()) {
                    $statusCode = $response->getStatusCode();
                    $body = (string)$response->getBody();
                    $data = json_decode($body, true);

                    $errorMessage = $response->getReasonPhrase();

                    if (!empty($data['desc'])) {
                        $errorMessage = $data['desc'];
                    }

                    if (!empty($data['attrs']['detail'])) {
                        $errorMessage = $data['attrs']['detail'];
                    }

                    $this->errorResult(sprintf('Provider API Error: %s', $errorMessage), [
                        'http_code' => $statusCode,
                        'response' => $data ?? $body,
                    ], [], $e);
                }
            }

            $this->errorResult('Provider API Request Failed', [
                'exception_type' => get_class($e),
                'exception' => $e->getMessage(),
            ], [], $e);
        }

        throw $e;
    }
}
