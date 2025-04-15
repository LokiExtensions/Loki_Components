<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Location;

use Magento\Framework\ObjectManagerInterface;
use Yireo\LokiComponents\Location\Address\BusinessHours;

class LocationFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(
        string $id,
        string $code,
        string $label,
        int $distance,
        Address $address,
        ?BusinessHours $businessHours = null,
    ): Location
    {
        return $this->objectManager->create(Location::class, [
            'id' => $id,
            'code' => $code,
            'label' => $label,
            'distance' => $distance,
            'address' => $address,
            'businessHours' => $businessHours,
        ]);
    }
}
