<?php
declare(strict_types=1);

namespace Loki\Components\Location\Address\BusinessHour;

use Magento\Framework\ObjectManagerInterface;

class DayPartFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $arguments = []): DayPart
    {
        return $this->objectManager->create(DayPart::class, $arguments);
    }
}
