<?php
declare(strict_types=1);

namespace Loki\Components\Location\Address\BusinessHour;

use Magento\Framework\ObjectManagerInterface;

class DayFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $arguments = []): Day
    {
        return $this->objectManager->create(Day::class, $arguments);
    }
}
