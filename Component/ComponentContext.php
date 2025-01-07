<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\ObjectManagerInterface;

class ComponentContext
{
    public function __construct(
        public readonly ObjectManagerInterface $objectManager,
    ) {
    }

    public function getCustomerSession(): CustomerSession
    {
        return $this->objectManager->get(CustomerSession::class);
    }

    public function getRequest(): HttpRequest
    {
        return $this->objectManager->get(HttpRequest::class);
    }
}
