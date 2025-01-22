<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlFactory;

class ComponentContext implements ComponentContextInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly UrlFactory $urlFactory,
    ) {
    }

    public function getCustomerSession(): CustomerSession
    {
        return $this->customerSession;
    }

    public function getUrlFactory(): UrlFactory
    {
        return $this->urlFactory;
    }
}
