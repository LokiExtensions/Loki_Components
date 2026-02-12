<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Magento\Framework\ObjectManagerInterface;
use Loki\Components\Location\Address\BusinessHours;

class LocationFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(
        Address $address,
        ?BusinessHours $businessHours = null,
        string $id = '',
        string $code = '',
        string $label = '',
        int $distance = 0,
        ?string $pickupFrom = null,
        ?int $price = null,
        array $data = [],
    ): Location {
        return $this->objectManager->create(Location::class, [
            'address' => $address,
            'businessHours' => $businessHours,
            'id' => $id,
            'code' => $code,
            'label' => $label,
            'distance' => $distance,
            'pickupFrom' => $pickupFrom,
            'price' => $price,
            'data' => $data
        ]);
    }
}
