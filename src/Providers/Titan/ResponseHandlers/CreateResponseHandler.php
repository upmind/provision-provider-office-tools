<?php
declare(strict_types=1);

namespace Upmind\ProvisionProviders\OfficeTools\Providers\Titan\ResponseHandlers;

class CreateResponseHandler extends ResponseHandler
{
    /**
     * Assert 'Add Domain' was successful.
     *
     * @throws OperationFailed If "Create" failed
     */
    public function assertSuccess(): void{
        try {
            parent::assertSuccess();

            $message = strtolower($this->getBody());

            if (Str::contains($message, 'success:')) {
                return;
            }

            if (Str::contains($message, 'already exists')) {
                throw new CannotParseResponse('Domain name already exists');
            }

            if (Str::containsAll($message, ['domain name', 'is incorrect'])) {
                throw new CannotParseResponse('Service identifier is not a valid domain name');
            }

            throw new CannotParseResponse('Failed to add domain name');
        } catch (CannotParseResponse $e) {
            throw (new OperationFailed($e->getMessage(), 0, $e))
                ->withDebug([
                    'http_code' => $this->response->getStatusCode(),
                    'content_type' => $this->response->getHeaderLine('Content-Type'),
                    'body' => $this->getBody(),
                ]);
        }
    }
}
