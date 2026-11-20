<?php

declare(strict_types=1);

namespace Loki\Components\Controller\Index;

use Loki\Components\Polling\PollingHandlerListing;
use Loki\Components\Util\Controller\FormKeyHeader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Polling implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonResultFactory $jsonResultFactory,
        private readonly PollingHandlerListing $pollingHandlerListing,
        private readonly FormKeyHeader $formKeyHeader,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = [];
        foreach ($this->pollingHandlerListing->getHandlers() as $handler) {
            $data = array_merge($data, $handler->execute());
        }

        $jsonResult = $this->jsonResultFactory->create();
        $jsonResult->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private', true);
        $jsonResult->setHeader('Pragma', 'no-cache', true);
        $jsonResult->setData($data);

        return $jsonResult;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        $jsonResult = $this->jsonResultFactory->create();
        $jsonResult->setHttpResponseCode(400);
        $jsonResult->setData(['error' => true, 'message' => (string)__('Invalid Form Key. Please refresh the page')]);

        return new InvalidRequestException($jsonResult, [__('The request is not valid.')]);
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return $this->formKeyHeader->validate($request);
    }
}
