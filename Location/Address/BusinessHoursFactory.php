<?php
declare(strict_types=1);

namespace Loki\Components\Location\Address;

use Magento\Framework\ObjectManagerInterface;

class BusinessHoursFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $arguments = []): BusinessHours
    {
        return $this->objectManager->create(BusinessHours::class, $arguments);
    }
}
