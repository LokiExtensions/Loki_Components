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
        string $id,
        string $code,
        string $label,
        int $distance,
        Address $address,
        ?BusinessHours $businessHours = null,
        ?string $pickupFrom = null,
        ?int $price = null,
    ): Location {
        return $this->objectManager->create(Location::class, [
            'id' => $id,
            'code' => $code,
            'label' => $label,
            'distance' => $distance,
            'address' => $address,
            'businessHours' => $businessHours,
            'pickupFrom' => $pickupFrom,
            'price' => $price,
        ]);
    }
}
