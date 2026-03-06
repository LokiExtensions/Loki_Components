<?php

declare(strict_types=1);

namespace Loki\Components\Controller\Index;

use Loki\Components\Polling\PollingHandlerListing;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Polling implements HttpGetActionInterface
{
    public function __construct(
        private readonly JsonResultFactory $jsonResultFactory,
        private readonly PollingHandlerListing $pollingHandlerListing,
    ) {
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $data = [];
        foreach ($this->pollingHandlerListing->getHandlers() as $handler) {
            $data = array_merge($data, $handler->execute());
        }

        $jsonResult = $this->jsonResultFactory->create();
        $jsonResult->setData($data);
        return $jsonResult;
    }
}
