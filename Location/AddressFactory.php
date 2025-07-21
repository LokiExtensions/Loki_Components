<?php declare(strict_types=1);

namespace Loki\Components\Location;

use Magento\Framework\ObjectManagerInterface;

class AddressFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create(
        string $street,
        string $houseNumber,
        string $postcode,
        string $city,
        string $countryId,
        ?float $latitude = null,
        ?float $longitude = null,
        string $comment = '',
    ): Address {
        return $this->objectManager->create(Address::class, [
            'street' => $street,
            'houseNumber' => $houseNumber,
            'postcode' => $postcode,
            'city' => $city,
            'countryId' => $countryId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'comment' => $comment,
        ]);
    }
}
