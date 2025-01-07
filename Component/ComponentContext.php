<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Magento\Customer\Model\Session as CustomerSession;

class ComponentContext implements ComponentContextInterface
{
    public function __construct(
        private CustomerSession $customerSession
    ) {
    }

    public function getCustomerSession(): CustomerSession
    {
        return $this->customerSession;
    }
}
